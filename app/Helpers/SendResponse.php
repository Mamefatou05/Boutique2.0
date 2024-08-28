<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use App\Enums\StatutEnum;

class SendResponse
{
    /**
     * Generate a JSON response.
     *
     * @param mixed $data
     * @param int $status
     * @param ResponseStatus $statusType
     * @param string|null $message
     * @param string|null $link
     * @return JsonResponse
     */
    public static function jsonResponse(
        $data,
        int $status,
        StatutEnum $statusType,
        string $message = null,
        string $link = null
    ): JsonResponse {
        $responseArray = [
            'status' => $status,
            'success' => $statusType->value, // Enum value
            'message' => $message,
            'data' => $data,
        ];

        if ($link !== null) {
            $responseArray['link'] = $link;
        }

        return response()->json($responseArray, $status);
    }
}
