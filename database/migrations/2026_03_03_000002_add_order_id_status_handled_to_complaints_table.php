<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            if (! Schema::hasColumn('complaints', 'order_id')) {
                $table->unsignedBigInteger('order_id')->nullable()->after('seller_id');
                $table->foreign('order_id')->references('id')->on('orders')->nullOnDelete();
            }

            if (! Schema::hasColumn('complaints', 'status')) {
                $table->string('status', 20)->default('open')->after('rating');
            }

            if (! Schema::hasColumn('complaints', 'handled_by')) {
                $table->unsignedBigInteger('handled_by')->nullable()->after('status');
                $table->foreign('handled_by')->references('id')->on('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('complaints', 'handled_at')) {
                $table->timestamp('handled_at')->nullable()->after('handled_by');
            }

            // Unique constraint: 1 rating per order per buyer
            // Cek apakah index sudah ada sebelum menambahkan
        });

        // Tambah unique index secara terpisah agar bisa dicek dengan hasIndex-like approach
        // Gunakan try-catch karena Schema tidak punya hasIndex bawaan
        try {
            Schema::table('complaints', function (Blueprint $table) {
                $table->unique(['buyer_id', 'order_id'], 'complaints_buyer_order_unique');
            });
        } catch (\Exception $e) {
            // Index sudah ada, skip
        }
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            // Drop unique index
            try {
                $table->dropUnique('complaints_buyer_order_unique');
            } catch (\Exception $e) {
                // Index tidak ada, skip
            }

            if (Schema::hasColumn('complaints', 'handled_at')) {
                $table->dropColumn('handled_at');
            }
            if (Schema::hasColumn('complaints', 'handled_by')) {
                $table->dropForeign(['handled_by']);
                $table->dropColumn('handled_by');
            }
            if (Schema::hasColumn('complaints', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('complaints', 'order_id')) {
                $table->dropForeign(['order_id']);
                $table->dropColumn('order_id');
            }
        });
    }
};
