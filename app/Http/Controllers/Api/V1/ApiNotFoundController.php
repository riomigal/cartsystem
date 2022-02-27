<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ApiNotFoundController extends Controller
{
    /**
     * @hideFromAPIDocumentation
     *
     * Handles 404 Invalid routes (not found)
     * @return JsonResponse
     */
    public function notFound(): JsonResponse
    {
        return response()->json(['message' =>  'Invalid route'], 404);
    }
}
