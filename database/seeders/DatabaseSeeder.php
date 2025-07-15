<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // اگر خواستی این‌ها رو هم فعال کن
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // این خط باعث اجرای UserSeeder می‌شه
        $this->call([
            UserSeeder::class,
        ]);
    }
}
