<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlayerResource;
use App\Mail\PasswordReset;
use App\Models\Player;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    /**
     * Handle an incoming password reset link request.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'channel_id' => ['required'],
            'email' => ['required', 'email'],
        ]);

        DB::beginTransaction();
        try {
            $player = Player::where('channel_id', $request->channel_id)->where('email', $request->email)->first();

            if ($player) {
                $otp = random_int(100000, 999999);
                $player->update(['otp_code' => $otp, 'otp_expiry' => Carbon::now()->addMinutes(10)]);

                DB::commit();

                Mail::to($player->email)->send(new PasswordReset($otp));
                return response()->json('OTP sent to your email successfully.', Response::HTTP_OK);
            }

            return response()->json("We can't find a player for your email.", Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error($ex);
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle an incoming OTP request.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'channel_id' => ['required'],
            'email' => ['required', 'email'],
            'otp_code' => ['required'],
        ]);

        DB::beginTransaction();
        try {
            $player = Player::where('channel_id', $request->channel_id)
                ->where('email', $request->email)
                ->where('otp_code', $request->otp_code)
                ->where('otp_expiry', '>=', Carbon::now())
                ->first();

            if ($player) {
                $player->update(['otp_code' => null, 'otp_expiry' => null]);

                DB::commit();
                return PlayerResource::make($player)->additional(['accessToken' => $player->createToken('accessToken')->plainTextToken]);
            }

            return response()->json("OTP Invalid or Expired.", Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error($ex);
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
