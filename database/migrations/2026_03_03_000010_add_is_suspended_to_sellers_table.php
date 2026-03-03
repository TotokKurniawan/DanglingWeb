<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            if (! Schema::hasColumn('sellers', 'is_suspended')) {
                $table->boolean('is_suspended')->default(false)->after('is_online');
            }
            if (! Schema::hasColumn('sellers', 'suspended_reason')) {
                $table->string('suspended_reason')->nullable()->after('is_suspended');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn(['is_suspended', 'suspended_reason']);
        });
    }
};
