<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\VoteRequest;
use App\Http\Resources\VoteResource;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoteController extends Controller
{
    /**
     * Created by: Charith Gamage
     * Created at: 2023-06-20
     *
     * Display a current vote.
     */
    public function index(Request $request)
    {
        try {
            $rating = Rating::where('channel_id', auth()->user()->channel_id)->firstWhere('game_id', $request->gameId);
            $vote = auth()->user()->votes()->firstWhere('game_id', $request->gameId)->vote ?? 0;

            $data = [
                'rating' => $rating,
                'vote' => $vote,
            ];

            return VoteResource::make($data);
        } catch (\Exception $ex) {
            Log::error($ex);
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Created by: Charith Gamage
     * Created at: 2023-06-20
     *
     * Store a newly created resource in storage.
     */
    public function store(VoteRequest $request)
    {
        DB::beginTransaction();
        try {
            $channelId = auth()->user()->channel->id;
            $gameId = $request->game_id;

            $oldVote = auth()->user()->votes->firstWhere('game_id', $gameId);
            $rating = Rating::where('channel_id', $channelId)->firstWhere('game_id', $gameId);

            if ($request->vote === 1) {
                $this->like($channelId, $gameId, $oldVote, $rating);
            } elseif ($request->vote === -1) {
                $this->dislike($channelId, $gameId, $oldVote, $rating);
            } else {
                $this->remove($oldVote, $rating);
            }

            DB::commit();

            $rating = Rating::where('channel_id', $channelId)->firstWhere('game_id', $gameId);
            $vote = auth()->user()->votes()->firstWhere('game_id', $gameId)->vote ?? 0;

            $data = [
                'rating' => $rating,
                'vote' => $vote,
            ];

            return VoteResource::make($data);
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error($ex);
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Created by: Charith Gamage
     * Created at: 2023-06-20
     *
     * Like vote.
     */
    private function like($channelId, $gameId, $oldVote, $rating)
    {
        if ($oldVote && $oldVote->vote === -1) {
            $oldVote->update(['vote' => 1]);

            $likes = $rating->likes + 1;
            $dislikes = $rating->dislikes - 1;
            $rating->update($this->rating($likes, $dislikes));
        } elseif (!$oldVote) {
            auth()->user()->votes()->create(['game_id' => $gameId, 'vote' => 1]);
            if ($rating) {
                $likes = $rating->likes + 1;
                $dislikes = $rating->dislikes;
                $rating->update($this->rating($likes, $dislikes));
            } else {
                Rating::create(['channel_id' => $channelId, 'game_id' => $gameId, 'likes' => 1, 'rating' => 5]);
            }
        }
    }

    /**
     * Created by: Charith Gamage
     * Created at: 2023-06-20
     *
     * Dislike vote.
     */
    private function dislike($channelId, $gameId, $oldVote, $rating)
    {
        if ($oldVote && $oldVote->vote === 1) {
            $oldVote->update(['vote' => -1]);
            $likes = $rating->likes - 1;
            $dislikes = $rating->dislikes + 1;
            $rating->update($this->rating($likes, $dislikes));
        } elseif (!$oldVote) {
            auth()->user()->votes()->create(['game_id' => $gameId, 'vote' => -1]);
            if ($rating) {
                $likes = $rating->likes;
                $dislikes = $rating->dislikes + 1;
                $rating->update($this->rating($likes, $dislikes));
            } else {
                Rating::create(['channel_id' => $channelId, 'game_id' => $gameId, 'dislikes' => 1]);
            }
        }
    }

    /**
     * Created by: Charith Gamage
     * Created at: 2023-06-20
     *
     * Remove vote.
     */
    private function remove($oldVote, $rating)
    {
        if ($oldVote && $rating) {
            if ($oldVote->vote === 1) {
                $likes = $rating->likes - 1;
                $dislikes = $rating->dislikes;
                $rating->update($this->rating($likes, $dislikes));
            } elseif ($oldVote->vote === -1) {
                $likes = $rating->likes;
                $dislikes = $rating->dislikes - 1;
                $rating->update($this->rating($likes, $dislikes));
            }

            $oldVote->delete();
        }
    }

    /**
     * Created by: Charith Gamage
     * Created at: 2023-06-20
     *
     * Rating calculating.
     */
    private function rating($likes, $dislikes)
    {
        $total = $likes + $dislikes;
        $rating = $total ? ($likes / $total * 100) / 20 : 0;

        return ['likes' => $likes, 'dislikes' => $dislikes, 'rating' => $rating];
    }
}
