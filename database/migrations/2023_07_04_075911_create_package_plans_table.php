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
        Schema::create('package_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Package::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\PackageTerm::class)->constrained()->cascadeOnDelete();
            $table->decimal('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_plans');
    }
};
