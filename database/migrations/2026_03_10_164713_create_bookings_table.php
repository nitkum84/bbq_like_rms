<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('bookings')) {
            return;
        }

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('table_id')->constrained('tables')->onDelete('cascade');
            $table->foreignId('slot_id')->constrained('time_slots')->onDelete('cascade');
            $table->date('booking_date');
            $table->enum('meal_type', ['lunch', 'dinner']);
            $table->integer('veg_count')->default(0);
            $table->integer('nonveg_count')->default(0);
            $table->json('guest_type')->nullable()->comment('e.g. [kids, anniversary]');
            $table->boolean('offer_applied')->default(false);
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->onDelete('set null');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->string('confirmation_code', 20)->nullable()->unique();
            $table->boolean('sms_sent')->default(false);
            $table->boolean('email_sent')->default(false);
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('bookings'); }
};
