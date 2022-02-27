<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use function App\Components\logArrayToString;

class LogRequest
{

    /**
     * Handle an incoming request from api.
     * Sets a requests uuid to track errors in the logs
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return Response|JsonResponse
     */
    public function handle($request, Closure $next): Response|JsonResponse
    {
        $requestId = (string) Str::uuid();


        if (!App::environment('PRODUCTION')) {
            Log::withContext([
                'request-id' => $requestId
            ]);
        }


        $response = $next($request)->header('Request-Id', $requestId);

        $log = "URI: " . $request->getUri() . "\n" .
            "METHOD: " . $request->getMethod() . "\n" .
            "Request IP: " . $request->ip() . "\n" .
            "HEADERS: " . json_encode($request->header(), JSON_PRETTY_PRINT)  . "\n\n" .
            "REQUEST_BODY: " . json_encode($request->all(), JSON_PRETTY_PRINT) . "\n\n" .
            "RESPONSE: \n\n " . json_encode(json_decode($response->getContent(), true), JSON_PRETTY_PRINT) . "\n";


        Log::debug("LogRequest: " . $requestId . "\n REQUEST: \n\n" . $log . "\n");

        return $response;
    }
}
