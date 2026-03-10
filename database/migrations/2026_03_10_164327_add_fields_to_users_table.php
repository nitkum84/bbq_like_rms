<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile', 15)->nullable()->after('email');
            $table->string('profile_image')->nullable()->after('mobile');
            $table->timestamp('mobile_verified_at')->nullable()->after('email_verified_at');
            $table->tinyInteger('status')->default(1)->comment('1=active, 0=inactive')->after('mobile_verified_at');
            $table->string('otp', 6)->nullable()->after('status');
            $table->timestamp('otp_expires_at')->nullable()->after('otp');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mobile','profile_image','mobile_verified_at','status','otp','otp_expires_at']);
        });
    }
};
