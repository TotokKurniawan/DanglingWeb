<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('keluhans', function (Blueprint $table) {
            $table->dropForeign(['id_pembeli']);
            $table->dropForeign(['id_pedagang']);
        });
        DB::statement('ALTER TABLE keluhans MODIFY id_pembeli BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE keluhans MODIFY id_pedagang BIGINT UNSIGNED NULL');
        Schema::table('keluhans', function (Blueprint $table) {
            $table->foreign('id_pembeli')->references('id')->on('pembelis')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_pedagang')->references('id')->on('pedagangs')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('keluhans', function (Blueprint $table) {
            $table->dropForeign(['id_pembeli']);
            $table->dropForeign(['id_pedagang']);
        });
        DB::statement('ALTER TABLE keluhans MODIFY id_pembeli BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE keluhans MODIFY id_pedagang BIGINT UNSIGNED NOT NULL');
        Schema::table('keluhans', function (Blueprint $table) {
            $table->foreign('id_pembeli')->references('id')->on('pembelis')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_pedagang')->references('id')->on('pedagangs')->onDelete('cascade')->onUpdate('cascade');
        });
    }
};
