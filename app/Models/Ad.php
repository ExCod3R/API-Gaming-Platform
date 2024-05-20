<?php

namespace App\Models;

use App\Enums\AdCategoryEnum;
use App\Models\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ad extends Model
{
    use HasFactory;
    use Multitenantable;

    protected $guarded = [];

    protected $casts = [
        'ad_category' =>  AdCategoryEnum::class,
    ];

    public function counties(): BelongsToMany
    {
        return $this->belongsToMany(Country::class);
    }
}
