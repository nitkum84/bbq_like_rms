<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('deals_bundle_menu_item')) {
            return;
        }

        Schema::create('deals_bundle_menu_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deals_bundle_id')->constrained('deals_bundles')->onDelete('cascade');
            $table->foreignId('menu_item_id')->constrained('menu_items')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['deals_bundle_id', 'menu_item_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('deals_bundle_menu_item');
    }
};
