<?php

namespace App\Http\Controllers\Auth;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ProfileRequest;
use App\Http\Requests\Auth\RegisteredRequest;
use App\Http\Resources\PlayerResource;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Handle an incoming login request.
     */
    public function login(LoginRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->authenticate();

            $user = auth()->guard('api')->user();

            DB::commit();
            return PlayerResource::make($user)->additional(['accessToken' => $user->createToken('accessToken')->plainTextToken]);
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error($ex);
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle an incoming social login request.
     */
    public function socialLogin(Request $request)
    {
        DB::beginTransaction();
        try {
            $provider = $request->provider;
            $token = $request->access_token;
            $providerUser = Socialite::driver($provider)->userFromToken($token);

            $user = Player::where('channel_id', $request->channel_id)->where('email', $providerUser->email)->first();
            if ($user == null) {
                $input = [
                    'channel_id' => $request->channel_id,
                    'provider' => $provider,
                    'provider_id' => $providerUser->id,
                    'name' => $providerUser->name,
                    'email' => $providerUser->email,
                ];

                if ($providerUser->avatar) {
                    $input['avatar'] = $this->handleImageUrlFileUpload($providerUser->avatar, 'avatars');
                }

                $user = Player::create($input)->assignRole(RoleEnum::PLAYER->value);
            }

            DB::commit();
            return PlayerResource::make($user)->additional(['accessToken' => $user->createToken('accessToken')->plainTextToken]);
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error($ex);
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(RegisteredRequest $request)
    {
        DB::beginTransaction();
        try {
            $player = Player::create([
                'channel_id' => $request->channel_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'country' => $request->country,
                'password' => Hash::make($request->password),
            ])->assignRole(RoleEnum::PLAYER->value);

            // event(new Registered($player));
            DB::commit();
            return PlayerResource::make($player)->additional(['accessToken' => $player->createToken('accessToken')->plainTextToken]);
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error($ex);
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get authenticated user.
     */
    public function show()
    {
        $user = auth()->user();
        $user['packagePlan'] = auth()->user()
            ->packagePlans()
            ->with(['package', 'packageTerm'])
            ->where('unsubscribed_at', '>', now())
            ->orderBy('subscribed_at', 'asc')
            ->first();

        return PlayerResource::make($user);
    }

    /**
     * Handle an incoming profile update request.
     */
    public function update(ProfileRequest $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->only(['name', 'email', 'phone', 'country']);

            if (isset($request->avatar)) {
                $input['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            auth()->user()->update($input);
            $user = auth()->user();

            DB::commit();
            return PlayerResource::make($user);
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error($ex);
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('user')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
