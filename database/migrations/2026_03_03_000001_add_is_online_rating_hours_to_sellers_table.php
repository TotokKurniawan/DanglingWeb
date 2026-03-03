<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            // Gantikan field 'status' (string) dengan boolean is_online yang lebih eksplisit
            if (! Schema::hasColumn('sellers', 'is_online')) {
                $table->boolean('is_online')->default(false)->after('status');
            }

            if (! Schema::hasColumn('sellers', 'rating_average')) {
                $table->decimal('rating_average', 3, 2)->nullable()->after('is_online');
            }

            if (! Schema::hasColumn('sellers', 'rating_count')) {
                $table->unsignedInteger('rating_count')->default(0)->after('rating_average');
            }

            if (! Schema::hasColumn('sellers', 'open_time')) {
                $table->time('open_time')->nullable()->after('rating_count');
            }

            if (! Schema::hasColumn('sellers', 'close_time')) {
                $table->time('close_time')->nullable()->after('open_time');
            }
        });

        // Migrasi data: seller dengan status 'online' → is_online = true
        DB::table('sellers')->where('status', 'online')->update(['is_online' => true]);
        DB::table('sellers')->where('status', '!=', 'online')->update(['is_online' => false]);
    }

    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            // Kembalikan is_online → status sebelum drop
            DB::table('sellers')->where('is_online', true)->update(['status' => 'online']);
            DB::table('sellers')->where('is_online', false)->update(['status' => 'offline']);

            $cols = ['is_online', 'rating_average', 'rating_count', 'open_time', 'close_time'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('sellers', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
