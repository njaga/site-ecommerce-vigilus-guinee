<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Caméras IP', 'Caméras HDCVI', 'Enregistreurs NVR', 'Enregistreurs XVR',
            'Kits vidéosurveillance', 'Contrôle d’accès', 'Interphonie',
            'Alarmes', 'Réseaux & PoE', 'Stockage & accessoires',
        ];

        foreach ($categories as $order => $name) {
            Category::firstOrCreate(['slug' => Str::slug($name)], [
                'name' => $name,
                'sort_order' => $order + 1,
                'is_active' => true,
            ]);
        }

        $email = trim((string) env('ADMIN_EMAIL'));
        $password = (string) env('ADMIN_PASSWORD');

        if ($email !== '' || $password !== '') {
            if (! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 14) {
                throw new RuntimeException('ADMIN_EMAIL valide et ADMIN_PASSWORD de 14 caractères minimum sont requis.');
            }

            User::updateOrCreate(['email' => $email], [
                'name' => 'Administration Vigilus',
                'password' => $password,
                'is_admin' => true,
            ]);
        }

        // Aucune référence ni aucun prix Dahua fictif ne sont injectés.
    }
}
