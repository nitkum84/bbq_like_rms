<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('deals_bundles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['veg', 'non-veg', 'mixed']);
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'flat'])->default('percentage');
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->date('valid_from');
            $table->date('valid_to');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('deals_bundles'); }
};
