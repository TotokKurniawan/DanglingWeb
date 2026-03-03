<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->string('event', 100);           // e.g. order.created, complaint.submitted
                $table->string('subject_type')->nullable(); // e.g. App\Models\Order
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->json('properties')->nullable();  // data tambahan (before/after, metadata)
                $table->string('ip_address', 45)->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index(['event']);
                $table->index(['subject_type', 'subject_id']);
                $table->index(['user_id']);
                $table->index(['created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
