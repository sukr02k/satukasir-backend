<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(
            ['name' => 'Super Admin'],
            ['id' => 3]
        );

        User::updateOrCreate(
            ['email' => 'admin@kasirsatu.my.id'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@kasirsatu.my.id',
                'password' => Hash::make('admin123'),
                'role_id' => 3,
                'phone' => null,
                'business_id' => null,
                'outlet_id' => null,
            ]
        );

        $this->command->info('Super Admin created successfully!');
        $this->command->info('Email: admin@kasirsatu.my.id');
        $this->command->info('Password: admin123');
    }
}