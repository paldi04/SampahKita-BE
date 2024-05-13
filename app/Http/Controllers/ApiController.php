<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message = 'Permintaan Anda telah berhasil diproses!', $code = 200)
    {
    	$response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($errorMessage, $errorData = [], $code = 500)
    {
    	$response = [
            'success' => false,
            'message' => $errorMessage,
        ];

        if(!empty($errorData)){
            $response['data'] = $errorData;
        }

        return response()->json($response, $code);
    }
}