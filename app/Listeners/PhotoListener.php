<?php

namespace App\Listeners;

use App\Events\PhotoEvent;
use App\Jobs\ProcessPhotoJob;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;
use App\Services\UploadService;
use App\Services\UploadCloudService;
use Illuminate\Contracts\Queue\ShouldQueue;

class PhotoListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */

    /**
     * Handle the event.
     */

    public function handle(PhotoEvent $event)
    {
        Log::debug("PhotoEvent") ;
        // Dispatcher le job asynchrone
        // ProcessPhotoJob::dispatch($event->filePath , $event->user);
    }
    
}
