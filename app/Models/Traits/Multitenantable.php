<?php

namespace App\Models\Traits;

use App\Enums\RoleEnum;
use Illuminate\Contracts\Database\Eloquent\Builder;

trait Multitenantable
{
    protected static function bootMultitenantable(): void
    {
        if (auth()->check() && !auth()->user()->hasRole(RoleEnum::SUPER_ADMIN->value)) {
            static::creating(function ($model) {
                $model->channel_id = auth()->user()->channel_id;
            });

            static::addGlobalScope('created_by_channel', function (Builder $builder) {
                $builder->where('channel_id', auth()->user()->channel_id);
            });
        }
    }
}
