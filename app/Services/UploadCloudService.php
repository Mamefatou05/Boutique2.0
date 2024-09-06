<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\UploadedFileInterface;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UploadCloudService implements UploadServiceInterface
{

    

    public function uploadFile($file)
    {
        if ($file) {
            Log::debug("Uploading file - File found: " . $file->getClientOriginalName());
            
            try {
                $uploadResponse = Cloudinary::upload($file->getRealPath(), [
                    'folder' => 'images',
                ]);
    
                $uploadedFileUrl = $uploadResponse->getSecurePath();
    
                Log::info("File uploaded successfully", ['url' => $uploadedFileUrl]);
    
                return $uploadedFileUrl;
            } catch (\Exception $e) {
                Log::error("File upload failed", ['error' => $e->getMessage()]);
                return null;
            }
        }
    
        Log::warning("No file provided for upload");
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

    public function isImage($file)

    {
        return in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif']);
    }

    public function generateQrCode($data)
    {
        $qrCode = QrCode::format('png')->size(200)->generate($data);
        return base64_encode($qrCode);
    }
    public function deleteFile($publicId)
    {
        Cloudinary::destroy($publicId);  // Supprime le fichier sur Cloudinary
    }
}
