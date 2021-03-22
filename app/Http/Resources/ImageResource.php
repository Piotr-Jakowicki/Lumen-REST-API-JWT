<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => (int)$this->user_id,
            'title' => $this->title,
            'path' => $this->path,
            'categories' => CategoryResource::collection($this->whenLoaded('categories'))
        ];
    }
}
