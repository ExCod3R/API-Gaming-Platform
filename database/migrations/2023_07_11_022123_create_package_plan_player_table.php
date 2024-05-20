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
        Schema::create('package_plan_player', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Player::class)->constrained();
            $table->foreignIdFor(\App\Models\PackagePlan::class)->constrained();
            $table->timestamp('subscribed_at');
            $table->timestamp('unsubscribed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_plan_player');
    }
};
