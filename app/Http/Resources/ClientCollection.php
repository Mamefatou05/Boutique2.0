<?php


namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ClientCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->transform(function ($client) {
                return new ClientResource($client);
            }),
            'links' => [
                'self' => 'link-value',
            ],
        ];
    }
}
