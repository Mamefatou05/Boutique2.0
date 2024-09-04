<?php
namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

class UserQrCodeEmail extends Mailable
{
    public $qrCodeBase64;

    public function __construct($qrCodeBase64)
    {
        $this->qrCodeBase64 = $qrCodeBase64;
    }

    public function build()
    {
        return $this->view('emails.qr_code') // Utilisation de la vue créée
                    ->attachData(
                        base64_decode($this->qrCodeBase64),
                        'qr_code.png',
                        [
                            'mime' => 'image/png',
                        ]
                    );
    }
}
