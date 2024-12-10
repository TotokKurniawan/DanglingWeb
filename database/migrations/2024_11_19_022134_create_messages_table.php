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
        Schema::create('messages', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->text('message'); // Isi pesan
            $table->unsignedBigInteger('id_pembeli'); // Foreign Key ke tabel pembelis
            $table->unsignedBigInteger('id_pedagang'); // Foreign Key ke tabel pedagangs
            $table->timestamps(); // Kolom created_at dan updated_at

            // Foreign keys
            $table->foreign('id_pembeli')->references('id')->on('pembelis')->onDelete('cascade');
            $table->foreign('id_pedagang')->references('id')->on('pedagangs')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
