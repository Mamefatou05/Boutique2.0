<?php

namespace App\Http\Middleware;

use App\Enums\StatutEnum;
use App\Helpers\SendResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
{
    $response = $next($request);

    // Vérifiez si la réponse est une instance de Response
    if (!$response instanceof Response) {
        return $response;
    }

    // Obtenez le code de statut et le contenu de la réponse
    $statusCode = $response->getStatusCode();
    $data = $response->getContent();

    // Décodez le contenu JSON si c'est une chaîne JSON valide
    $decodedData = json_decode($data, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        if ($this->isAlreadyFormatted($decodedData)) {
            return $response;
        }
        $data = $decodedData;
    }

    // Créez un nouveau message et type de statut
    $message = $this->getMessageForStatusCode($statusCode);
    $statusType = $statusCode < 400 ? StatutEnum::SUCCESS : StatutEnum::FAILURE;

    // Utilisez la méthode jsonResponse pour reformater la réponse
    return SendResponse::jsonResponse($data, $statusCode, $statusType, $message);
}


    private function isAlreadyFormatted(array $data): bool
    {
        Log::debug($data);

        Log::debug(isset($data['status'], $data['success'], $data['message']));
        // Vérifiez si toutes les clés spécifiques sont présentes dans le tableau
        return isset($data['status'], $data['success'], $data['message']);
    }
    

    private function getMessageForStatusCode($statusCode)
    {
        $messages = [
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            422 => 'Unprocessable Entity',
            500 => 'Internal Server Error',
        ];

        return $messages[$statusCode] ?? 'Unknown Status';
    }
}
