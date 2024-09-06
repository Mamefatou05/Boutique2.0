<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf as PDF;

class PdfGeneratorService
{
    static public function generateLoyalCardPdf($client, $user)
    {
        // Assurez-vous que les deux variables sont définies et passent bien
        $data = [
            'client' => $client,
            'user' => $user
        ];


        // Générer le PDF en utilisant une vue Blade
        $pdf = PDF::loadView('emails.qr_code', ['user' => $user, 'client' => $client]);
        
        return $pdf;
    }
}
