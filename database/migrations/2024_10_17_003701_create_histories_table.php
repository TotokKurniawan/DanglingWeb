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
        Schema::create('historys', function (Blueprint $table) {
            $table->id();
            $table->enum('status',['Menunggu','Diterima','Ditolak','done gk bang']);
            $table->string('bentuk_pembayaran'); 
            $table->unsignedBigInteger('id_pembeli'); 
            $table->unsignedBigInteger('id_pedagang');
            $table->timestamps();

            
            $table->foreign('id_pembeli')->references('id')->on('pembelis')->onDelete('cascade');
            $table->foreign('id_pedagang')->references('id')->on('pedagangs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('histories', function (Blueprint $table) {
            // Menghapus foreign key constraints sebelum menghapus tabel
            $table->dropForeign(['id_pembeli']);
            $table->dropForeign(['id_pedagang']);
        });

        Schema::dropIfExists('historys'); // Tidak perlu `table:` di sini
    }
};
