<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function jsonResponse($data, $status = 200, $message = "Success", $links = null): JsonResponse
    {
        $response = [
            "data" => $data,
            "status" => $status,
            "message" => $message,
        ];
    
        // Ajoute les liens seulement s'ils sont fournis
        if ($links) {
            $response['links'] = $links;
        }
    
        return response()->json($response, $status);
    }
    
}
