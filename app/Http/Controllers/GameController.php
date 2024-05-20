<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\GamePageResource;
use App\Http\Resources\GameTileResource;
use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{
    /**
     * Created by: Charith Gamage
     * Created at: 2023-07-11
     *
     * Get game path if user subscribed to the correct package.
     */
    public function gamePath(Request $request)
    {
        try {
            $game = auth()->user()->channel->games->find($request->gameId);

            if ($game->pivot->is_paid) {
                $activePackagePlan = auth()->user()
                    ->packagePlans()
                    ->where('unsubscribed_at', '>', now())
                    ->orderBy('subscribed_at', 'asc')
                    ->first();

                $gameFolderPath = $activePackagePlan?->package?->games?->find($request->gameId)?->game_folder;
            } else {
                $gameFolderPath = $game?->game_folder;
            }

            if ($gameFolderPath) {
                return response()->json(asset('storage/' . $gameFolderPath), Response::HTTP_OK);
            } else {
                return response()->json('You\'re not eligible to play this game.', Response::HTTP_UNAUTHORIZED);
            }
        } catch (\Exception $ex) {
            Log::error($ex);
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Created by: Charith Gamage
     * Created at: 2023-06-16
     *
     * Display a listing of the resource.
     */
    public function index(Request $request, $channelId)
    {
        try {
            $games = Channel::findOrFail($channelId)->games()->whereStatus(true);
            $games = $request->search ? $games->where('name', 'LIKE', "%{$request->search}%")->orderBy('pivot_order_column')->get() : $games->orderBy('pivot_order_column')->get();

            return GameTileResource::collection($games);
        } catch (\Exception $ex) {
            Log::error($ex);
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Created by: Charith Gamage
     * Created at: 2023-06-16
     *
     * Display the specified resource.
     */
    public function show($channelId, $slug)
    {
        try {
            $channel = Channel::with(['games'])->findOrFail($channelId);

            $selectGame = $channel->games->where('slug', $slug)->firstOrFail();
            $otherGames = $channel->games->where('slug', '!=', $slug);

            $rating = 0;
            $totalVote = 0;
            $ratings = $selectGame->ratings()->where('channel_id', $channelId)->firstOrFail();
            if ($ratings) {
                $rating = $ratings->rating;
                $totalVote = $ratings->likes + $ratings->dislikes;
            }

            $data = [
                'selectGame' => $selectGame,
                'otherGames' => $otherGames,
                'rating' => $rating,
                'totalVote' => $totalVote,
            ];

            return GamePageResource::make($data);
        } catch (\Exception $ex) {
            Log::error($ex);
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
