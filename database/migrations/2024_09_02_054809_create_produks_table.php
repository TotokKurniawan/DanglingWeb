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
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk');
            $table->integer('harga_produk');
            $table->enum('kategori_produk', ['makanan ringan', 'makanan berat','jasa']);
            $table->string('foto');
            $table->unsignedBigInteger('id_pedagang');
            $table->timestamps();
            $table->foreign('id_pedagang')->references('id')->on('pedagangs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
