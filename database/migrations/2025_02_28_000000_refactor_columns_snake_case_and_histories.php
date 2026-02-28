<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Refactor: snake_case columns & fix inconsistencies on existing DB.
     * Uses raw SQL to avoid doctrine/dbal dependency.
     */
    public function up(): void
    {
        if (Schema::hasTable('mitras') && Schema::hasColumn('mitras', 'Perusahaan')) {
            DB::statement('ALTER TABLE mitras CHANGE Perusahaan perusahaan VARCHAR(255)');
        }

        if (Schema::hasTable('pembelis') && Schema::hasColumn('pembelis', 'longtitude')) {
            DB::statement('ALTER TABLE pembelis CHANGE longtitude longitude DECIMAL(8,2) NULL');
        }

        if (Schema::hasTable('histories') && !Schema::hasColumn('histories', 'alasan_tolak')) {
            Schema::table('histories', function (Blueprint $table) {
                $table->string('alasan_tolak')->nullable()->after('bentuk_pembayaran');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('mitras') && Schema::hasColumn('mitras', 'perusahaan')) {
            DB::statement('ALTER TABLE mitras CHANGE perusahaan Perusahaan VARCHAR(255)');
        }

        if (Schema::hasTable('pembelis') && Schema::hasColumn('pembelis', 'longitude')) {
            DB::statement('ALTER TABLE pembelis CHANGE longitude longtitude DECIMAL(8,2) NULL');
        }

        if (Schema::hasTable('histories') && Schema::hasColumn('histories', 'alasan_tolak')) {
            Schema::table('histories', function (Blueprint $table) {
                $table->dropColumn('alasan_tolak');
            });
        }
    }
};
