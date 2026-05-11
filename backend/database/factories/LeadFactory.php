<?php

namespace Database\Factories;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'id'               => Str::uuid(),
            'name'             => fake()->name(),
            'whatsapp'         => fake()->numerify('+55 11 9####-####'),
            'instagram'        => fake()->optional()->userName(),
            'source'           => fake()->randomElement(['INSTAGRAM', 'WHATSAPP', 'REFERRAL', 'WEBSITE', 'EVENT']),
            'interest'         => fake()->sentence(3),
            'destination'      => fake()->randomElement(['Chapada dos Veadeiros', 'Patagônia', 'Serra da Canastra', 'Pantanal', 'Lençóis Maranhenses']),
            'date_desired'     => fake()->dateTimeBetween('+1 month', '+1 year')->format('Y-m-d'),
            'people_count'     => fake()->numberBetween(1, 8),
            'estimated_ticket' => fake()->randomFloat(2, 500, 15000),
            'status'           => fake()->randomElement(['NEW', 'CONTACTED', 'QUALIFIED', 'PROPOSAL', 'RESERVED', 'PAID']),
            'notes'            => null,
            'last_contact'     => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'next_follow_up'   => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'tags'             => [],
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => ['status' => 'PAID']);
    }
}
