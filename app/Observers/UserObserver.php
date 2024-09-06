<?php

namespace App\Observers;

use App\Events\PhotoEvent;
use App\Jobs\ProcessPhotoJob;
use App\Models\User;
use App\Services\UploadCloudService;
use App\Services\UploadService;
use Illuminate\Support\Facades\Log;

class UserObserver
{

    protected $uploadService;

    public function __construct()
    {
        // Initialisez le service d'upload
        $this->uploadService = app('App\Services\UploadService');
    }
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
    }
   // UserObserver
public function creating(User $user)
{
    try {
        if (!empty($user->photo)) {
            $filePath = $user->photo->store('temp'); // Stockage temporaire

              ProcessPhotoJob::dispatch($filePath, $user);
            
            Log::info("Utilisateur en cours de création: {$user->login}");
        }
    } catch (\Exception $e) {
        Log::error("Erreur lors de la création de l'utilisateur : " . $e->getMessage());
        throw $e;
    }
}

    


    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }

   

    /**
     * Lorsqu'un utilisateur est récupéré.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function retrieved(User $user)
    {
        try {
            // Assurez-vous que la relation 'role' est chargée
            $user->load('role');

            // Vérifier et traiter la photo
            if (!empty($user->photo)) {
                // Si c'est une URL, on l'utilise directement
                if (!$this->isUrl($user->photo)) {
                    // Sinon, on traite comme un fichier local et on la convertit en base64
                    $photoBase64 = $this->uploadService->saveImageAsBase64($user->photo);
                    $user->photo = $photoBase64;
                    Log::info("La photo locale a été convertie en Base64.");
                } else {
                    Log::info("L'utilisateur a fourni une URL en ligne pour la photo.");
                    // On garde l'URL telle quelle
                }
            }

        } catch (\Exception $e) {
            Log::error("Erreur lors du traitement de la photo pour l'utilisateur : " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Vérifie si la chaîne est une URL valide.
     *
     * @param  string  $string
     * @return bool
     */
    private function isUrl($string)
    {
        return filter_var($string, FILTER_VALIDATE_URL) !== false;
    }
}









