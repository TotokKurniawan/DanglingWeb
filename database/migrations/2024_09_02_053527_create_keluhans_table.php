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
        Schema::create('keluhans', function (Blueprint $table) {
            $table->id();
            $table->string('deskripsi');
            $table->integer('rating');
            $table->unsignedBigInteger('id_pembeli'); // Ganti spasi dengan underscore
            $table->unsignedBigInteger('id_pedagang'); // Ganti spasi dengan underscore
            $table->timestamps();

            // Menambahkan foreign key constraints
            $table->foreign('id_pembeli')->references('id')->on('pembelis')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_pedagang')->references('id')->on('pedagangs')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keluhans', function (Blueprint $table) {
            if (Schema::hasColumn('keluhans', 'id_pembeli')) {
                $table->dropForeign(['id_pembeli']);
            }
            if (Schema::hasColumn('keluhans', 'id_pedagang')) {
                $table->dropForeign(['id_pedagang']);
            }
        });
        Schema::dropIfExists('keluhans');
    }
};
