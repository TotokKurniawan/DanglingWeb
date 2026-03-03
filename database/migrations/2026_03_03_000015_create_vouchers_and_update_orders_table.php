<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('vouchers')) {
            Schema::create('vouchers', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->enum('type', ['percentage', 'fixed']);
                $table->integer('value'); // Nilai persen atau nilai flat
                $table->integer('min_purchase')->default(0);
                $table->integer('max_discount')->nullable(); // Maksimal diskon jika type percentage
                $table->dateTime('valid_until')->nullable();
                $table->integer('limit')->nullable(); // Batas klaim
                $table->integer('claimed_count')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Tambah kolom ke orders
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'voucher_id')) {
                $table->unsignedBigInteger('voucher_id')->nullable()->after('payment_status');
                $table->foreign('voucher_id')->references('id')->on('vouchers')->nullOnDelete();
            }
            if (! Schema::hasColumn('orders', 'discount_amount')) {
                $table->integer('discount_amount')->default(0)->after('voucher_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['voucher_id']);
            $table->dropColumn(['voucher_id', 'discount_amount']);
        });

        Schema::dropIfExists('vouchers');
    }
};
