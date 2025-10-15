<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création des rôles par défaut
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Accès complet à toutes les fonctionnalités',
            ],
            [
                'name' => 'Administrateur',
                'slug' => 'admin',
                'description' => 'Accès à la plupart des fonctionnalités administratives',
            ],
            [
                'name' => 'Gestionnaire',
                'slug' => 'manager',
                'description' => 'Gestion des contenus et modération',
            ],
            [
                'name' => 'Éditeur',
                'slug' => 'editor',
                'description' => 'Création et édition de contenu uniquement',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
