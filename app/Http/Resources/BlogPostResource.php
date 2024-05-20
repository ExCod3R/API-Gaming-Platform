<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'banner' => asset('storage/' . $this->banner),
            'is_url' => $this->is_url,
            'url' => $this->url,
            'content' => $this->content,
            'date' => $this->created_at->format('M d, Y'),
            'blog_categories' => BlogCategoryResource::collection($this->whenLoaded('blogCategories')),
        ];
    }
}
