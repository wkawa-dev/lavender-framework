<?php

namespace Database\Factories;

use App\Models\AgentClient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<AgentClient>
 */
class AgentClientFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company().' agent',
            'client_id' => (string) Str::uuid(),
            'secret' => 'secret',
        ];
    }
}
