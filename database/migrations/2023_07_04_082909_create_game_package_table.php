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
        Schema::create('game_package', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Game::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Package::class)->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_package');
    }
};
