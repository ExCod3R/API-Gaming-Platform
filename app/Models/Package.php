<?php

namespace App\Models;

use App\Models\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;
    use Multitenantable;

    protected $guarded = [];

    public function packagePlans()
    {
        return $this->hasMany(PackagePlan::class);
    }

    public function games()
    {
        return $this->belongsToMany(Game::class);
    }
}
