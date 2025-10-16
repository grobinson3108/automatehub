<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer l'utilisateur admin par défaut
        User::create([
            'name' => 'Administrateur',
            'first_name' => 'Admin',
            'last_name' => 'Système',
            'username' => 'admin',
            'email' => 'admin@automatehub.fr',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123!'),
            'level_n8n' => 'expert',
            'subscription_type' => 'premium',
            'is_admin' => true,
            'is_professional' => false,
            'last_activity_at' => now(),
        ]);

        // Créer un utilisateur de test premium
        User::create([
            'name' => 'Utilisateur Premium',
            'first_name' => 'Pierre',
            'last_name' => 'Martin',
            'username' => 'premium',
            'email' => 'premium@automatehub.fr',
            'email_verified_at' => now(),
            'password' => Hash::make('premium123!'),
            'level_n8n' => 'intermediate',
            'subscription_type' => 'premium',
            'is_admin' => false,
            'is_professional' => false,
            'last_activity_at' => now(),
        ]);

        // Créer un utilisateur professionnel
        User::create([
            'name' => 'Jean Dupont',
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'username' => 'produpont',
            'email' => 'pro@automatehub.fr',
            'email_verified_at' => now(),
            'password' => Hash::make('pro123!'),
            'level_n8n' => 'intermediate',
            'subscription_type' => 'premium',
            'is_admin' => false,
            'is_professional' => true,
            'company_name' => 'Entreprise Test SARL',
            'company_address' => '123 Rue de la Paix',
            'company_postal_code' => '75001',
            'company_city' => 'Paris',
            'company_country' => 'France',
            'company_vat' => 'FR12345678901',
            'phone' => '+33123456789',
            'last_activity_at' => now(),
        ]);

        // Créer un utilisateur gratuit
        User::create([
            'name' => 'Utilisateur Gratuit',
            'first_name' => 'Sophie',
            'last_name' => 'Durand',
            'username' => 'freesophie',
            'email' => 'free@automatehub.fr',
            'email_verified_at' => now(),
            'password' => Hash::make('free123!'),
            'level_n8n' => 'beginner',
            'subscription_type' => 'free',
            'is_admin' => false,
            'is_professional' => false,
            'last_activity_at' => now(),
        ]);
    }
}
