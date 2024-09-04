<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'libelle' => $this->name,
            'description' => $this->description,
            'qteStock' => $this->quantity_in_stock,
            'prixUnitaire' => $this->price,
            'links' => [
                'self' => route('articles.show', $this->id),
            ],
        ];
    }

    /**
     * Add additional information to the response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request): array
    {
        if ($this->resource instanceof ResourceCollection) {
            return [
                'meta' => [
                    'total_articles' => $this->collection->count(),
                ],
            ];
        }
    
        // Retourner un tableau vide si ce n'est pas une collection
        return [];
    }
    
}
