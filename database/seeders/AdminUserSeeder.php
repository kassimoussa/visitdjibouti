<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Trouver le rôle SuperAdmin
        $superAdminRole = Role::where('slug', 'super-admin')->first();

        if ($superAdminRole) {
            // Créer un admin par défaut
            AdminUser::create([
                'name' => 'Admin Système',
                'email' => 'admin@visitdjibouti.dj',
                'password' => Hash::make('password'), // À changer en production !
                'role_id' => $superAdminRole->id,
                'email_verified_at' => now(),
                'is_active' => true,
            ]);
        }
    }
}