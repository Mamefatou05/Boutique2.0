<?php

namespace App\Jobs;

use App\Services\PdfGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendQrCodeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $client;
    protected $user;

    public function __construct($client, $user,)
    {
        $this->client = $client;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $pdf = PdfGeneratorService::generateLoyalCardPdf($this->client, $this->user);

        Mail::send('emails.qr_code', ['user' => $this->user, 'client' => $this->client], function ($message) use ($pdf) {
            $message->to($this->client->email)
                ->subject('Votre carte de fidélité')
                ->attachData($pdf->output(), 'carte_fidelite.pdf', [
                    'mime' => 'application/pdf',
                ]);
        });
    }
}
