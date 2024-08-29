<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function sendResponse($message, $data = [], $code = 200)
    {
        $response = [
            'status' => 'success',
            'message' => $message
        ];

        if(!empty($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    public function sendError($message, $errors = [], $code = 500)
    {
        $response = [
            'status' => 'failed',
            'message' => $message
        ];

        if(!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}
