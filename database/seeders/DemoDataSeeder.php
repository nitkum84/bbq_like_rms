<?php
namespace Database\Seeders;

use App\Models\MenuCategory;
use App\Models\PricingRule;
use App\Models\Table;
use App\Models\TimeSlot;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $tables = [
            ['table_number' => 'T01', 'seating_capacity' => 2, 'location' => 'Ground Floor', 'status' => 'active'],
            ['table_number' => 'T02', 'seating_capacity' => 4, 'location' => 'Ground Floor', 'status' => 'active'],
            ['table_number' => 'T03', 'seating_capacity' => 4, 'location' => 'Ground Floor', 'status' => 'active'],
            ['table_number' => 'T04', 'seating_capacity' => 6, 'location' => 'Terrace', 'status' => 'active'],
            ['table_number' => 'T05', 'seating_capacity' => 6, 'location' => 'Terrace', 'status' => 'active'],
            ['table_number' => 'T06', 'seating_capacity' => 8, 'location' => 'Private Room', 'status' => 'active'],
        ];

        foreach ($tables as $table) {
            Table::firstOrCreate(['table_number' => $table['table_number']], $table);
        }

        $slots = [
            ['slot_label' => '12:00 PM - 1:00 PM', 'start_time' => '12:00', 'end_time' => '13:00', 'meal_type' => 'lunch', 'max_bookings' => 5],
            ['slot_label' => '1:00 PM - 2:00 PM', 'start_time' => '13:00', 'end_time' => '14:00', 'meal_type' => 'lunch', 'max_bookings' => 5],
            ['slot_label' => '2:00 PM - 3:00 PM', 'start_time' => '14:00', 'end_time' => '15:00', 'meal_type' => 'lunch', 'max_bookings' => 5],
            ['slot_label' => '7:00 PM - 8:00 PM', 'start_time' => '19:00', 'end_time' => '20:00', 'meal_type' => 'dinner', 'max_bookings' => 5],
            ['slot_label' => '8:00 PM - 9:00 PM', 'start_time' => '20:00', 'end_time' => '21:00', 'meal_type' => 'dinner', 'max_bookings' => 5],
            ['slot_label' => '9:00 PM - 10:00 PM', 'start_time' => '21:00', 'end_time' => '22:00', 'meal_type' => 'dinner', 'max_bookings' => 5],
        ];

        foreach ($slots as $slot) {
            TimeSlot::firstOrCreate(['slot_label' => $slot['slot_label']], $slot + ['is_active' => true]);
        }

        $categories = [
            ['name' => 'Starters', 'type' => 'both', 'display_order' => 1],
            ['name' => 'Veg Main Course', 'type' => 'veg', 'display_order' => 2],
            ['name' => 'Non-Veg Main Course', 'type' => 'non-veg', 'display_order' => 3],
            ['name' => 'Breads & Rice', 'type' => 'both', 'display_order' => 4],
            ['name' => 'Desserts', 'type' => 'both', 'display_order' => 5],
            ['name' => 'Beverages', 'type' => 'both', 'display_order' => 6],
        ];

        foreach ($categories as $category) {
            MenuCategory::firstOrCreate(['name' => $category['name']], $category + ['is_active' => true]);
        }

        $adminId = \App\Models\User::whereHas('roles', fn ($query) => $query->where('name', 'super-admin'))
            ->value('id') ?? 1;

        PricingRule::firstOrCreate(
            ['day_type' => 'weekday'],
            ['price' => 499, 'effective_date' => today(), 'created_by' => $adminId]
        );

        PricingRule::firstOrCreate(
            ['day_type' => 'weekend'],
            ['price' => 699, 'effective_date' => today(), 'created_by' => $adminId]
        );
    }
}
