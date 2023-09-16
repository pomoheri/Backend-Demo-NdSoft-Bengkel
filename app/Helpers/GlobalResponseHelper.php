<?php 

namespace App\Helpers;

class GlobalResponseHelper
{   
    /**
    * Success response method
    *
    * @param $data
    * @param $message
    * @return \Illuminate\Http\JsonResponse
    */
    public function sendResponse($data = null, $message = '', $status = true)
    {
        $response = [
            'status'  => true,
            'message' => $message,
            'data'    => $data,
        ];

        return response()->json($response, 200);
    }

    /**
     * Return error response
     *
     * @param       $error
     * @param array $errorMessages
     * @param int   $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError($error, $errorMessages = [], $code = 400)
    {
        $response = [
            'status'  => false,
            'message' => $error,
        ];
    
        !empty($errorMessages) ? $response['data'] = $errorMessages : null;
    
        return response()->json($response, $code);

    }
}