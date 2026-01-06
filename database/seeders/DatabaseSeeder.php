<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Restaurant;
use App\Models\FoodItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        $admin = Student::where('email', 'admin@campuscafe.com')->first();
        if (!$admin) {
            $admin = Student::create([
                'name' => 'System Administrator',
                'email' => 'admin@campuscafe.com',
                'password' => Hash::make('admin123'),
                'phone' => '012-3456789',
                'is_admin' => true,
            ]);
            echo "Admin created: admin@campuscafe.com / admin123\n";
        }

        // Create test student if not exists
        $student = Student::where('email', 'test@student.com')->first();
        if (!$student) {
            $student = Student::create([
                'name' => 'Test Student',
                'email' => 'test@student.com',
                'password' => Hash::make('password123'),
                'phone' => '012-3456789',
                'is_admin' => false,
            ]);

            $student->cart()->create(['total' => 0]);
            echo "Student created: test@student.com / password123\n";
        }

        // Seed restaurants and food items
        $this->call(RestaurantSeeder::class);

        echo "\nDatabase seeded successfully!\n";
        echo "===============================\n";
        echo "Admin Account:\n";
        echo "Email: admin@campuscafe.com\n";
        echo "Password: admin123\n";
        echo "\nStudent Account:\n";
        echo "Email: test@student.com\n";
        echo "Password: password123\n";
    }
}