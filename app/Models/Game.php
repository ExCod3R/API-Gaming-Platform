<?php

namespace App\Models;

use App\Enums\OrientationEnum;
use App\Enums\TileSizeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use ZipArchive;

class Game extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'orientation' =>  OrientationEnum::class,
        'tile_size' =>  TileSizeEnum::class,
    ];

    public function channels()
    {
        return $this->belongsToMany(Channel::class)
            ->withPivot(['tile_size', 'is_hot', 'is_highlight', 'is_paid', 'order_column'])
            ->withTimestamps();
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            unzip($model);
        });

        static::updating(function ($model) {
            if (File::exists(storage_path('app/public/' . $model->game_folder))) {
                File::deleteDirectory(storage_path('app/public/' . $model->game_folder));
            }

            unzip($model);
        });

        function unzip($model)
        {
            if (isset($model->game_file)) {
                $zipFilePath = storage_path('app/public/' . $model->game_file);

                $zip = new ZipArchive;
                $res = $zip->open($zipFilePath);
                if ($res === true) {
                    $gameFolderLocation = 'games/' . str_replace(" ", "", ucwords($model->name));

                    $zip->extractTo(storage_path('app/public/' . $gameFolderLocation));
                    $zip->close();

                    $model->game_folder = $gameFolderLocation;
                }
            }
        }
    }
}
