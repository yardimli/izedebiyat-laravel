<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\LlmSetting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(QuoteSeeder::class);

        LlmSetting::query()->firstOrCreate([], [
            'frontend_model' => config('llm.frontend_model'),
            'backend_model' => config('llm.backend_model'),
            'cron_model' => config('llm.cron_model'),
            'allowed_frontend_models' => null,
        ]);

    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
}
