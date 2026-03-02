<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Latitude/longitude already defined on sellers table in base migration.
    }

    public function down(): void
    {
        // No-op.
    }
};
