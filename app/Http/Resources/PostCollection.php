<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PostCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        // Must return an array!
        return [
            'data' => $this->collection->transform(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->content,
                    'user' => [
                        'id' => $post->user->id ?? null,
                        'name' => $post->user->name ?? null,
                        'email' => $post->user->email ?? null,
                    ],
                    'created_at' => $post->created_at->toDateTimeString(),
                ];
            }),
        ];
    }

}
