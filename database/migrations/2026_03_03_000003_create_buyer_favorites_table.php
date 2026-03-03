<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('buyer_favorites')) {
            Schema::create('buyer_favorites', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('buyer_id');
                $table->unsignedBigInteger('seller_id');
                $table->timestamps();

                $table->foreign('buyer_id')->references('id')->on('buyers')->cascadeOnDelete();
                $table->foreign('seller_id')->references('id')->on('sellers')->cascadeOnDelete();
                $table->unique(['buyer_id', 'seller_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('buyer_favorites');
    }
};
