<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScoreRequest;
use App\Http\Resources\LeaderBoardResource;
use App\Http\Resources\ScoreResource;
use App\Models\Score;
use Barryvdh\Snappy\Facades\SnappyImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScoreController extends Controller
{
    /**
     * Created by: Charith Gamage
     * Created at: 2023-06-23
     *
     * Display a leader board.
     */
    public function index(Request $request)
    {
        try {
            $score = Score::with(['player'])
                ->select(DB::raw('*, max(top_score) as score'))
                ->where('channel_id', auth()->user()->channel_id)
                ->where('game_id', $request->gameId);

            if ($request->type == 'daily') {
                $score->where('date', Carbon::now()->format('Y-m-d'));
            } elseif ($request->type == 'weekly') {
                $score->whereBetween('date', [Carbon::now()->subWeek()->format('Y-m-d'), Carbon::now()->format('Y-m-d')]);
            } else {
                $score->whereBetween('date', [Carbon::now()->subMonth()->format('Y-m-d'), Carbon::now()->format('Y-m-d')]);
            }

            $score = $score->orderBy('score', 'DESC')
                ->groupBy('player_id')
                ->paginate($request->limit ?? 10);

            return LeaderBoardResource::collection($score);
        } catch (\Exception $ex) {
            Log::error($ex);
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Created by: Charith Gamage
     * Created at: 2023-06-14
     *
     * Store a newly created resource in storage.
     */
    public function store(ScoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $decryptedData = $this->decryptAES256($request->data);
            $data = explode("-", $decryptedData);

            if (!isset($data[0]) || !isset($data[1])) {
                return response()->json('The payload is invalid.', Response::HTTP_BAD_REQUEST);
            }

            $gameId = $data[0];
            $newScore = (int) $data[1];

            $player = auth()->user();
            $score = Score::where('channel_id', $player->channel_id)
                ->where('player_id', $player->id)
                ->where('game_id', $gameId)
                ->where('date', date('Y-m-d'))
                ->first();

            if (isset($score)) {
                $input = ['last_score' => $newScore];
                if ($newScore > $score->top_score) {
                    $input += ['top_score' => $newScore];
                }

                $score->update($input);
            } else {
                $score = Score::create([
                    'channel_id' => $player->channel_id,
                    'player_id' => $player->id,
                    'game_id' => $gameId,
                    'last_score' => $newScore,
                    'top_score' => $newScore,
                    'date' => date('Y-m-d')
                ]);
            }

            DB::commit();
            return ScoreResource::make($score);
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error($ex);
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Created by: Charith Gamage
     * Created at: 2024-01-31
     *
     * Create a leader board image.
     */
    public function image(Request $request, $channelId)
    {
        try {
            $scores = Score::where('channel_id', $channelId)->where('game_id', $request->game_id)->orderBy('top_score', 'desc')->get();
            $image = SnappyImage::loadView('scores', ['scores' => $scores]);

            return $image->download('image.png');
        } catch (\Exception $ex) {
            Log::error($ex);
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
