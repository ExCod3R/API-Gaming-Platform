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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Channel::class)->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone')->nullable();
            $table->string('country')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('otp_code', 6)->nullable();
            $table->timestamp('otp_expiry')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
