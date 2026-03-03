<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('icon')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique('name');
            });
        }

        // Tambah category_id FK di products, pertahankan kolom category lama sementara
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('category');
                $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
            }
            if (! Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (! Schema::hasColumn('products', 'stock')) {
                $table->integer('stock')->nullable()->after('price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'description', 'stock']);
        });
        Schema::dropIfExists('categories');
    }
};
