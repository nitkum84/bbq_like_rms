<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder {
    public function run(): void {
        Role::firstOrCreate(['name' => 'super-admin']);
        Role::firstOrCreate(['name' => 'user']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Super Admin',
                'mobile'   => '9999999999',
                'password' => Hash::make('12345678'),
                'status'   => 1,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('super-admin');
        $this->command->info('Admin created: admin@gmail.com / 12345678');
    }
}
