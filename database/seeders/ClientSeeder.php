<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client; // Dodaj import modelu Client

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::factory()->count(50)->create(); // Tworzy 50 losowych klientów
    }
}

