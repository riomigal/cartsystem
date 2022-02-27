<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\Api\V1\ApiResponseHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class LoginController extends Controller
{
    use ApiResponseHandler;

    /**
     * Login
     *
     * Get a sanctum token to access the other endpoints.
     *
     * @group V1
     * @response scenario=success {
     *      token: JpzkCwV6JWQ7wEvky2CCrwOUAUfFPg14McdOoL6E
     * }
     * @response status=401 scenario="Wrong user or password" {"message": "Invalid Login Credentials"}
     * @unauthenticated
     *
     * @bodyParam email string required The email of the user. Example: john@doe.test
     * @bodyParam password string required The password of the user. Example: 12345678
     *
     * @param  Request $request
     * @return Response
     */
    public function login(Request $request): Response
    {

        // Validate request input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        // Catch validator errors and return json
        if ($validator->fails()) {
            return $this->handleError(2, [
                'message' => 'Invalid or missing credentials!',
                'errors' =>
                $validator->getMessageBag()
            ], 422);
        }

        // Check if user can login and has guard api
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return $this->handleError(1, ['message' => 'Invalid email or password'], 401);
        }

        // Create User token
        $token = $request->user()->createToken('cart_access', ['cart:access'])->plainTextToken;

        // Get only token string without id
        [$id, $token] = explode('|', $token, 2);

        return response(['token' => $token]);
    }
}
