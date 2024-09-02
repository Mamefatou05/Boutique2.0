<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
        return [
            'meta' => [
                'total_articles' => $this->collection->count(),
            ],
        ];
    }
}
