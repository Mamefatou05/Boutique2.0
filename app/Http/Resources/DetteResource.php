<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'id' => $this->id,
            'montant' => $this->montant,
            'montant_du'=>$this->montant_du,
            'montant_restant'=>$this->montant - $this->montant_du,
            'date_echeance' => $this->date,
            'client' => new ClientResource($this->whenLoaded('client')),  // Relation conditionnelle
            // 'links' => [
            //    'self' => route('dettes.show', ['dette' => $this->id]),
            //     'edit' => route('dettes.edit', ['dette' => $this->id]),
            // ],       
         ];

            
   


    }
}
