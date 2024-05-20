<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Traits\Multitenantable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Player extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;
    use Multitenantable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'channel_id',
        'provider',
        'provider_id',
        'name',
        'email',
        'phone',
        'country',
        'password',
        'otp_code',
        'otp_expiry',
        'avatar',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return true; // @todo Change this to check for access level
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function packagePlans()
    {
        return $this->belongsToMany(PackagePlan::class);
    }

    public function votes()
    {
        return $this->hasMany(PlayerVote::class);
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }
}
