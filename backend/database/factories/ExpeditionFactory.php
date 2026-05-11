<?php

namespace Database\Factories;

use App\Models\Expedition;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expedition>
 */
class ExpeditionFactory extends Factory
{
    protected $model = Expedition::class;

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+1 month', '+6 months');
        $end   = fake()->dateTimeBetween($start, '+7 months');

        return [
            'id'               => Str::uuid(),
            'name'             => 'Expedição ' . fake()->words(2, true),
            'cover_image'      => null,
            'destination'      => fake()->randomElement(['Chapada dos Veadeiros', 'Patagônia', 'Pantanal', 'Lençóis Maranhenses']),
            'dates'            => $start->format('d/m/Y') . ' a ' . $end->format('d/m/Y'),
            'start_date'       => $start->format('Y-m-d'),
            'end_date'         => $end->format('Y-m-d'),
            'capacity'         => fake()->numberBetween(8, 20),
            'remaining_spots'  => fake()->numberBetween(0, 10),
            'guide_id'         => null,
            'accommodation'    => fake()->randomElement(['Camping', 'Pousada', 'Hotel', 'Chalé']),
            'transport'        => fake()->randomElement(['Van', 'Ônibus', 'Próprio']),
            'trail_level'      => fake()->randomElement(['EASY', 'MODERATE', 'HARD', 'CHALLENGING']),
            'status'           => fake()->randomElement(['PLANNING', 'OPEN', 'GUARANTEED', 'IN_PROGRESS']),
            'costs'            => fake()->randomFloat(2, 1000, 50000),
            'margin_predicted' => fake()->randomFloat(2, 500, 10000),
            'margin_real'      => null,
            'participants'     => [],
        ];
    }

    public function open(): static
    {
        return $this->state(fn () => ['status' => 'OPEN']);
    }

    public function withParticipants(int $count = 3): static
    {
        return $this->state(fn () => [
            'participants' => array_map(fn () => Str::uuid(), range(1, $count)),
        ]);
    }
}
