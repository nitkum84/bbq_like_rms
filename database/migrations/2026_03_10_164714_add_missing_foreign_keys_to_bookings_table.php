<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('bookings') || DB::getDriverName() === 'sqlite') {
            return;
        }

        $foreignKeys = [
            'bookings_user_id_foreign' => ['column' => 'user_id', 'table' => 'users', 'onDelete' => 'cascade'],
            'bookings_table_id_foreign' => ['column' => 'table_id', 'table' => 'tables', 'onDelete' => 'cascade'],
            'bookings_slot_id_foreign' => ['column' => 'slot_id', 'table' => 'time_slots', 'onDelete' => 'cascade'],
            'bookings_voucher_id_foreign' => ['column' => 'voucher_id', 'table' => 'vouchers', 'onDelete' => 'set null'],
        ];

        foreach ($foreignKeys as $constraintName => $definition) {
            if (! Schema::hasTable($definition['table']) || ! Schema::hasColumn('bookings', $definition['column'])) {
                continue;
            }

            $exists = DB::table('information_schema.TABLE_CONSTRAINTS')
                ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
                ->where('TABLE_NAME', 'bookings')
                ->where('CONSTRAINT_NAME', $constraintName)
                ->exists();

            if ($exists) {
                continue;
            }

            Schema::table('bookings', function (Blueprint $table) use ($definition) {
                $foreign = $table->foreign($definition['column'])->references('id')->on($definition['table']);

                if ($definition['onDelete'] === 'cascade') {
                    $foreign->cascadeOnDelete();
                } else {
                    $foreign->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('bookings')) {
            return;
        }

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['table_id']);
            $table->dropForeign(['slot_id']);
            $table->dropForeign(['voucher_id']);
        });
    }
};
