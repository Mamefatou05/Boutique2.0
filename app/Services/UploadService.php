<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use \SimpleSoftwareIO\QrCode\Facades\QrCode  as QrCode;

class UploadService
{
    public function uploadFile($file, $directory = 'images')
    {
        if ($file) {
            // Générer un nom unique pour le fichier
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
    
            // Stocker le fichier dans 'public/images' sur le disque 'public'
            $path = $file->storeAs($directory, $filename, 'public');

    
            return $path;  // Le chemin sera 'images/nom_du_fichier.ext'
        }
        return null;
    }
    

    public function saveImageAsBase64($file)
    {
        if ($file && $this->isImage($file)) {

            $imageData = file_get_contents($file->getRealPath());
            return base64_encode($imageData);
        }
        return null;
    }

    private function isImage($file)
    {
        return in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif']);
    }

    public function generateQrCode($data)
    {
        $qrCode = QrCode::format('png')->size(200)->generate($data);
        return base64_encode($qrCode);
    }
    public function deleteFile($path)
    {
        Storage::delete($path);  // Supprime le fichier sur le disque
    }
}
