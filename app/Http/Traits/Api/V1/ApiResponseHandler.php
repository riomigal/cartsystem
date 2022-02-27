<?php

namespace App\Http\Traits\Api\V1;


use Carbon\Carbon;

/**
 * This trait handles the v5 Api responses
 */
trait ApiResponseHandler
{
    /* Response errors */
    protected $errors = [

        0 => ['description' => 'Status OK'],
        1 => ['description' => 'Invalid or Missing Login Credentials'],
        2 => ['description' => 'Bad Parameters'],
        99 => ['description' => 'Server error']
        /*    0       => ['description' => 'OK', 'severity' => 'OK'],
        1       => ['description' => 'Invalid request signature', 'severity' => 'FATAL'],
        2       => ['description' => 'Invalid other parameters', 'severity' => 'FATAL'],
        3       => ['description' => 'Insufficient credit', 'severity' => 'INFO MESSAGE'],
        4       => ['description' => 'Invalid Login credentials', 'severity' => 'FATAL'],
        5       => ['description' => 'Access denied! User has no permission to access this api.', 'severity' => 'FATAL'],
        6       => ['description' => 'Invalid or expired access token.', 'severity' => 'FATAL'],
        11       => ['description' => 'Unknown player', 'severity' => 'FATAL'],
        12       => ['description' => 'Invalid currency', 'severity' => 'FATAL'],
        13       => ['description' => 'Invalid amount', 'severity' => 'FATAL'],
        14       => ['description' => 'Invalid session state', 'severity' => 'FATAL'],
        15       => ['description' => 'Non-existent session', 'severity' => 'FATAL'],
        16       => ['description' => 'Invalid timestamp', 'severity' => 'FATAL'],
        20       => ['description' => 'Responsible gaming - session time reached', 'severity' => 'INFO MESSAGE'],
        22       => ['description' => 'Responsible gaming - daily bet limit reached', 'severity' => 'INFO MESSAGE'],
        23       => ['description' => 'Responsible gaming - daily loss limit reached', 'severity' => 'INFO MESSAGE'],
        24       => ['description' => 'Responsible gaming - the user is banned from playing .', 'severity' => 'FATAL'],
        25       => ['description' => 'Responsible gaming - monthly bet limit reached', 'severity' => 'INFO MESSAGE'],
        26       => ['description' => 'Responsible gaming - monthly loss limit reached', 'severity' => 'INFO MESSAGE'],
        27       => ['description' => 'Responsible gaming - hourly loss limit reached', 'severity' => 'INFO MESSAGE'],
        28       => ['description' => 'Responsible gaming - generic loss limit reached', 'severity' => 'INFO MESSAGE'],
        800       => ['description' => 'Request amount overflow', 'severity' => 'FATAL'],
        999       => ['description' => 'General error', 'severity' => 'FATAL'], */
    ];



    /**
     * Handles response successful
     *
     * @param array $data Add response data here
     *
     * @return object
     */
    protected function handleResponse(array $data): object
    {
        $responseArray = array_merge(
            ['result' => 0],
            $data,
            ['timestamp' => Carbon::now()->toISOString()]


        );

        return response()->json(
            $responseArray,
            200
        );
    }

    /**
     * Handles response errors
     *
     * @param int $resultId
     * @param array $data Just use if more information needed
     * @param int $code
     *
     * @return object
     */
    protected function handleError(int $resultId, array $data = [], int $code = 400, bool $displayError = false): object
    {

        $responseArray = array_merge(
            [
                'result' => $resultId,
                'msg' => $this->errors[$resultId]['description'],
                // 'display' => $displayError
            ],
            ['timestamp' => Carbon::now()->toISOString()],
            $data
        );


        return response()->json(
            $responseArray,
            $code
        );
    }
}
