<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UploadService
{
    public function uploadFile($file, $directory = 'uploads')
    {
        if ($file) {
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $path = Storage::disk('s3')->putFileAs($directory, $file, $filename);
            $url = Storage::disk('s3')->url($path);
            return $url; // Return the URL to store in the database
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
}
