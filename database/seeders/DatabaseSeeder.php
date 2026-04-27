<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\RfidCard;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@kantin.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Canteen Staff
        $canteen = User::create([
            'name' => 'Petugas Kantin',
            'email' => 'kantin@kantin.test',
            'password' => Hash::make('password'),
            'role' => 'canteen',
        ]);

        // Create Students
        $students = [
            ['name' => 'Ahmad Fauzi', 'email' => 'ahmad@kantin.test'],
            ['name' => 'Siti Nurhaliza', 'email' => 'siti@kantin.test'],
            ['name' => 'Budi Santoso', 'email' => 'budi@kantin.test'],
        ];

        foreach ($students as $student) {
            $user = User::create([
                'name' => $student['name'],
                'email' => $student['email'],
                'password' => Hash::make('password'),
                'role' => 'student',
            ]);

            // Create RFID card for each student
            RfidCard::create([
                'user_id' => $user->id,
                'rfid_uid' => strtoupper(fake()->bothify('##??##??')),
                'balance' => rand(50000, 200000),
            ]);
        }

        // Create Menu Items
        $menus = [
            ['name' => 'Nasi Goreng Spesial', 'price' => 15000, 'category' => 'makanan', 'stock' => 50, 'description' => 'Nasi goreng dengan telur, ayam, dan sayuran segar'],
            ['name' => 'Mie Ayam Bakso', 'price' => 12000, 'category' => 'makanan', 'stock' => 40, 'description' => 'Mie ayam dengan bakso sapi kenyal'],
            ['name' => 'Ayam Geprek', 'price' => 13000, 'category' => 'makanan', 'stock' => 30, 'description' => 'Ayam goreng crispy dengan sambal geprek pedas'],
            ['name' => 'Soto Ayam', 'price' => 10000, 'category' => 'makanan', 'stock' => 35, 'description' => 'Soto ayam kuah bening dengan bumbu rempah'],
            ['name' => 'Es Teh Manis', 'price' => 3000, 'category' => 'minuman', 'stock' => 100, 'description' => 'Es teh manis segar'],
            ['name' => 'Es Jeruk', 'price' => 5000, 'category' => 'minuman', 'stock' => 80, 'description' => 'Es jeruk peras segar'],
            ['name' => 'Jus Alpukat', 'price' => 8000, 'category' => 'minuman', 'stock' => 25, 'description' => 'Jus alpukat creamy dengan susu'],
            ['name' => 'Risol Mayo', 'price' => 3000, 'category' => 'snack', 'stock' => 60, 'description' => 'Risol isi mayonaise dan sayuran'],
            ['name' => 'Pisang Goreng', 'price' => 2000, 'category' => 'snack', 'stock' => 50, 'description' => 'Pisang goreng crispy'],
        ];

        foreach ($menus as $menu) {
            MenuItem::create($menu);
        }
    }
}
