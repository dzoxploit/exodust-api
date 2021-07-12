<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function generateResponse($data = [], $message = '', $statusCode = '')
    {
        $response = [
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data
        ];

        return response()->json($response, $statusCode);
    }

    protected function getUserId()
    {
        return Auth::id();
    }
}
