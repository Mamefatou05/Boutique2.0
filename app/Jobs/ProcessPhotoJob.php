<?php

namespace App\Jobs;

use App\Services\UploadService;
use App\Services\UploadCloudService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessPhotoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $uploadService;
    protected $uploadCloudService;
    protected $user;

    public function __construct($filePath, $user)
    {
        $this->filePath = $filePath;
       
        $this->user = $user;
    }

    public function handle()
    {
        $this->uploadService = app(UploadService::class);
        $this->uploadCloudService = app(UploadCloudService::class);
        
        Log::debug('handle upload request');
        try {
            $file = Storage::get($this->filePath);
            $uploadedUrl = $this->uploadCloudService->uploadFile($file);
            $this->user->photo = $uploadedUrl;
            $this->user->save(); // Enregistrer le chemin de la photo dans la base de données

            if (!$uploadedUrl) {
                throw new \Exception('Upload sur Cloudinary échoué');
            }

            Storage::delete($this->filePath); // Supprimer le fichier temporaire

            Log::info("Photo upload successful: " . $uploadedUrl);
        } catch (\Exception $e) {
            Log::error("Échec de l'upload sur Cloudinary, stockage local.", ['error' => $e->getMessage()]);
            $localPath = $this->uploadService->uploadFile($file);
            $this->user->photo = $localPath;
            $this->user->save(); // Enregistrer le chemin de la photo dans la base de données
            Log::info("Photo uploaded locally: " . $localPath);
        }
    }
}
