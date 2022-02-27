<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RespondWithJsonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return JsonResponse|Response
     */
    public function handle($request, Closure $next): JsonResponse|Response
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
