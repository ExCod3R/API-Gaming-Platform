<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\PackageResource;
use App\Models\Package;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PackageController extends Controller
{
    /**
     * Created by: Charith Gamage
     * Created at: 2023-07-11
     *
     * Display a listing of the resource.
     */
    public function index($channelId)
    {
        try {
            $packages = Package::with(['games', 'packagePlans', 'packagePlans.packageTerm'])->where('channel_id', $channelId)->whereStatus(true)->get();

            return PackageResource::collection($packages);
        } catch (\Exception $ex) {
            Log::error($ex);
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
