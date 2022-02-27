<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\Api\V1\ApiResponseHandler;
use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Product;
use App\Models\SerialNumber;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    use ApiResponseHandler;

    private Product $product;
    private SerialNumber|null $serialNumber;
    private Cart|null $cart;
    private CartProduct|null $cartProduct;
    private User $currentUser;

    /**
     * Get Cart
     *
     * Get Cart from current user
     *
     * @group V1
     * @response scenario=success {
     * "result": 0,
     * "cart_products": {
     *   "1": {
     *      "Product name": "Product1",
     *       "quantity": "20",
     *       "price": "98.65",
     *       "total_price": "1973"
     *   }
     * },
     * "cart_final_price": "1973",
     * "timestamp": "2022-02-27T00:34:40.188984Z"
     * }
     *
     * @param  Request $request
     * @return Response
     */
    public function index()
    {
        $this->cart = auth('sanctum')->user()->cart;


        if (!$this->cart || !$this->cart->cartProducts->first()) {
            return $this->handleResponse(['message' => 'Cart is empty!']);
        }

        $cartArray = [];
        foreach ($this->cart->cartProducts as $product) {

            $cartArray[$product->id] = [
                'Product name' => $product->product->name,
                'quantity' => $product->quantity,
                'price' => $product->price,
                'total_price' => $product->total_price
            ];
        }

        return $this->handleResponse([
            'cart_products' => $cartArray,
            'cart_final_price' => $this->cart->final_price
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Add new Product to Cart
     *
     * Add new products to cart
     *
     * @group V1
     * @response scenario=success {
     * "result": 0,
     * "message": "Product added to cart",
     * "product": {
     *    "id": 2,
     *   "name": "Product2",
     *   "quantity": 2,
     *   "price": "30.18",
     *   "total_price": 60.36
     * },
     * "available": 3,
     * "timestamp": "2022-02-27T00:39:23.408573Z"
     * }
     *
     * @bodyParam product_id integer required The product id. Example: 1
     * @bodyParam quantity integer required The quantity. Example: 1
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Start db transaction
        DB::beginTransaction();
        try {
            // Validate request input
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|numeric|exists:products,id',
                'quantity' => 'required|numeric|between:1,99'
            ]);

            // Catch validator errors and return json
            if ($validator->fails()) {
                return $this->handleError(2, [
                    'message' => 'Product parameter invalid!',
                    'errors' =>
                    $validator->getMessageBag()
                ], 422);
            }

            // Find product
            $this->product = Product::find($request->input('product_id'));


            // Check if product is sold out
            if ($this->product->quantity < 1) {
                return $this->handleError(0, [
                    'message' => 'Product sold out!',
                ], 202);
            }

            // Check if enough available for multiple quantity
            if ($this->product->quantity < $request->input('quantity')) {
                return $this->handleError(0, [
                    'message' => 'Not enough products available!',
                    'quantity_available' => $this->product->quantity
                ], 202);
            }



            // Update Product quantity
            $this->product->quantity -= $request->input('quantity');
            $this->product->save();

            $serialNumbersArray = [];

            // Get Product Serialnumber
            for ($x = 0; $x < $request->input('quantity'); $x++) {
                $this->serialNumber = $this->product->serialNumbers()->where('available', true)->first();
                $this->serialNumber->available = false;
                $this->serialNumber->save();
                $serialNumbersArray[] = $this->serialNumber->id;
            }


            // Get current user
            $this->currentUser = auth('sanctum')->user();


            // Find or create cart
            $this->cart = Cart::firstOrCreate(
                [
                    'user_id' => $this->currentUser->id
                ],
                [
                    'user_id' => $this->currentUser->id,
                    'final_price' => 0
                ]
            );


            // Find or create cart product
            $this->cartProduct = $this->cart->cartProducts()->where('product_id', $this->product->id)->first();

            if (!$this->cartProduct) {
                $this->cartProduct = CartProduct::create(
                    [
                        'cart_id' => $this->cart->id,
                        'product_id' => $this->product->id,
                        'serial_numbers' => $serialNumbersArray,
                        'quantity' => $request->input('quantity'),
                        'price' => $this->product->price,
                        'total_price' => $this->product->price * $request->input('quantity')
                    ]
                );
            } else {
                $this->cartProduct->serial_numbers = array_merge($this->cartProduct->serial_numbers, $serialNumbersArray);
                $this->cartProduct->quantity += $request->input('quantity');
                $this->cartProduct->total_price = $this->cartProduct->total_price + ($this->product->price * $request->input('quantity'));
                $this->cartProduct->update();
            }

            // Update Cart Total
            $this->cart->final_price = $this->cartProduct->sum('total_price');
            $this->cart->save();



            // Commit transaction
            DB::commit();

            return $this->handleResponse([
                'message' => 'Product added to cart',
                'product' => [
                    'id' => $this->cartProduct->id,
                    'name' => $this->product->name,
                    'quantity' => $this->cartProduct->quantity,
                    'price' => $this->cartProduct->price,
                    'total_price' => $this->cartProduct->total_price
                ],
                'available' => $this->product->quantity
            ]);
        } catch (Exception $e) {

            // Rollback transaction
            DB::rollBack();

            // Log error message
            Log::error($e->getMessage());

            return $this->handleError(99, [
                'message' => 'Something went wrong please contact admin.',
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cart $cart)
    {
        //
    }
}
