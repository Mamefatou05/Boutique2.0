<?php
namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

interface QrCodeEmailInterface extends Mailable
{

    public function build();
}
