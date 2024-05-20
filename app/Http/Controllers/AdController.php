<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdResource;
use App\Models\Ad;
use Illuminate\Http\Request;
use Torann\GeoIP\Facades\GeoIP;

class AdController extends Controller
{
    /**
     * Created by: Charith Gamage
     * Created at: 2024-05-15
     *
     * Display a listing of the resource.
     */
    public function index(Request $request, string $channelId)
    {
        $geoData = GeoIP::getLocation($request->ip());

        $ad = Ad::where('is_active', true)
            ->where('channel_id', $channelId)
            ->where('ad_category', $request->category)
            ->whereDate('published_date', '<=', now())
            ->whereDate('expire_date', '>=', now())
            ->whereColumn('view_limit', '>', 'total_views')
            ->where(function ($query) use ($geoData) {
                $query->whereHas('counties', function ($q) use ($geoData) {
                    $q->where('code', $geoData['iso_code']);
                })->orWhereDoesntHave('counties');
            })
            ->inRandomOrder()
            ->firstOrFail();

        // Increment the view count
        $ad->increment('total_views');

        return AdResource::make($ad);
    }
}
