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
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['Menunggu', 'Diterima', 'Ditolak', 'Selesai', 'Dibatalkan']);
            $table->string('bentuk_pembayaran');
            $table->string('alasan_tolak')->nullable();
            $table->unsignedBigInteger('id_pembeli');
            $table->unsignedBigInteger('id_pedagang');
            $table->timestamps();

            $table->foreign('id_pembeli')->references('id')->on('pembelis')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_pedagang')->references('id')->on('pedagangs')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('histories', function (Blueprint $table) {
            if (Schema::hasColumn('histories', 'id_pembeli')) {
                $table->dropForeign(['id_pembeli']);
            }
            if (Schema::hasColumn('histories', 'id_pedagang')) {
                $table->dropForeign(['id_pedagang']);
            }
        });
        Schema::dropIfExists('histories');
    }
};
