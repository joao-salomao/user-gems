<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name' => 'Stephan',
            'email' => 'stephan@usergems.com',
            'calendar_api_token' => '7S$16U^FmxkdV!1b'
        ]);

        User::factory()->create([
            'name' => 'Christian',
            'email' => 'christian@usergems.com',
            'calendar_api_token' => 'Ay@T3ZwF3YN^fZ@M'
        ]);

        User::factory()->create([
            'name' => 'Blaise',
            'email' => 'blaise@usergems.com',
            'calendar_api_token' => 'c0R*4iQK21McwLww'
        ]);

        User::factory()->create([
            'name' => 'Joss',
            'email' => 'joss@usergems.com',
            'calendar_api_token' => 'PK7UBPVeG%3pP9%B'
        ]);
    }
}
