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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Channel::class);
            $table->text('image');
            $table->string('name');
            $table->string('ad_category');
            $table->text('redirecting_link');
            $table->date('published_date');
            $table->date('expire_date');
            $table->integer('view_limit');
            $table->integer('total_views')->default(0);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
