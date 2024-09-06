<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'surname' => $this->surname,
            'telephone' => $this->telephone,
            'adresse' => $this->adresse,
            'photo' => $this->photo, // Attribut virtuel 'photo'
            'user' => new UserResource($this->whenLoaded('user')),
            'dettes' =>  DetteResource::collection($this->whenLoaded('dettes')), // Relation conditionnelle
            'links' => [
                'self' => route('clients.Find', ['id' => $this->id]),
            ],

        ];
    }
}
