<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UploadCloudService
{
    public function uploadFile($file, $directory = 'uploads')
    {
        if ($file) {
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $directory . '/' . $filename;
            Storage::disk('s3')->put($path, file_get_contents($file));
            $url = Storage::disk('s3')->url($path);
            return $url;
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
