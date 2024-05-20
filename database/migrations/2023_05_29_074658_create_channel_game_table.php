<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('channel_game', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Channel::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Game::class)->constrained()->cascadeOnDelete();
            $table->tinyInteger('tile_size')->default(0);
            $table->boolean('is_hot')->default(false);
            $table->boolean('is_highlight')->default(false);
            $table->boolean('is_paid')->default(false);
            $table->integer('order_column');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_game');
    }
};
