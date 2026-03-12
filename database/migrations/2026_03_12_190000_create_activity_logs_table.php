<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('causer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event', 50);
            $table->string('description')->nullable();
            $table->nullableMorphs('subject');
            $table->json('properties')->nullable();
            $table->string('route_name')->nullable();
            $table->string('method', 10)->nullable();
            $table->text('url')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['event', 'created_at']);
            $table->index('causer_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('activity_logs');
    }
};
