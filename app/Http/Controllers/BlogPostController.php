<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogPostResource;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogPostController extends Controller
{
    /**
     * Created by: Charith Gamage
     * Created at: 2024-02-29
     *
     * Display a listing of the random resource.
     */
    public function random(string $channelId)
    {
        $posts = BlogPost::with('blogCategories')
            ->where('channel_id', $channelId)
            ->where('is_active', true)
            ->inRandomOrder()
            ->take(2)->get();

        return BlogPostResource::collection($posts);
    }

    /**
     * Created by: Charith Gamage
     * Created at: 2024-02-29
     *
     * Display a listing of the resource.
     */
    public function index(Request $request, string $channelId)
    {
        $posts = BlogPost::with('blogCategories')
            ->where('channel_id', $channelId)
            ->where('is_active', true)
            ->when($request->has('search'), function ($query) use ($request) {
                $query->where('title', 'LIKE', "%{$request->input('search')}%");
            })
            ->when($request->has('category'), function ($query) use ($request) {
                $query->whereHas('blogCategories', function ($categoryQuery) use ($request) {
                    $categoryQuery->where('id', $request->input('category'));
                });
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(12);

        return BlogPostResource::collection($posts);
    }

    /**
     * Created by: Charith Gamage
     * Created at: 2024-02-29
     *
     * Display the specified resource.
     */
    public function show(string $channelId, string $slug)
    {
        $post = BlogPost::where('channel_id', $channelId)->where('slug', $slug)->firstOrFail();
        return BlogPostResource::make($post);
    }
}
