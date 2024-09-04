<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

interface UploadServiceInterface 
{
    public function uploadFile($file, $directory);
    
    public function saveImageAsBase64($file);

    public function isImage($file);
   
}
