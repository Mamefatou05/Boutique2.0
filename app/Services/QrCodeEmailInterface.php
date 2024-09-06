<?php
namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

interface QrCodeEmailInterface 
{

    public function build();
}
