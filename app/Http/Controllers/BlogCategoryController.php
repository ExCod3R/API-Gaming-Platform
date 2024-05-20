<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogCategoryResource;
use App\Models\BlogCategory;

class BlogCategoryController extends Controller
{
    /**
     * Created by: Charith Gamage
     * Created at: 2024-02-29
     *
     * Display a listing of the resource.
     */
    public function index(string $channelId)
    {
        $categories = BlogCategory::where('channel_id', $channelId)->get();
        return BlogCategoryResource::collection($categories);
    }
}
