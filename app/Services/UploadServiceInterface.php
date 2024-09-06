<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

interface UploadServiceInterface 
{
    public function uploadFile($file);
    
    public function saveImageAsBase64($file);

    public function isImage($file);

    public function generateQRCode($data);
   
}
