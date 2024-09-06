<?php

namespace App\Listeners;

use App\Events\SendQrCodeEmailEvent;
use App\Services\PdfGeneratorService; // Service pour générer le PDF
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendQrCodeEmailListener
{
    /**
     * Create the event listener.
     */
    protected $pdfGeneratorService;

    public function __construct(PdfGeneratorService $pdfGeneratorService)
    {
        $this->pdfGeneratorService = $pdfGeneratorService;
    }

    /**
     * Handle the event.
     */
    public function handle(SendQrCodeEmailEvent $event)
    {
        // Générer le PDF contenant la carte de fidélité
        $pdf = $this->pdfGeneratorService->generateLoyalCardPdf($event->client, $event->user );

        // Envoyer l'e-mail avec le PDF en pièce jointe
        Mail::send('emails.qr_code', ['user' => $event->user ,  'client' => $event->client], function ($message) use ($event, $pdf) {
            $message->to($event->client->email)
                ->subject('Votre carte de fidélité')
                ->attachData($pdf->output(), 'carte_fidelite.pdf', [
                    'mime' => 'application/pdf',
                ]);
        });
    }
}
