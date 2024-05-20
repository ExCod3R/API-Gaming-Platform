<?php

namespace App\Models;

use App\Models\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory;
    use Multitenantable;

    protected $guarded = [];

    public function BlogCategories()
    {
        return $this->belongsToMany(BlogCategory::class);
    }
}
