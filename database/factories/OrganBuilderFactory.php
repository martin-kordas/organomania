<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\Region;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrganBuilder>
 */
class OrganBuilderFactory extends Factory
{
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $regionIds = array_column(Region::cases(), 'value');
        
        return [
            'is_workshop' => false,
            'workshop_name' => null,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'place_of_birth' => fake()->city(),
            'place_of_death' => fake()->city(),
            'active_period' => fake()->randomElement([
                '19. stol.', '1. pol. 18. stol.', '1860-1911', '18.-19. stol.'
            ]),
            'active_from_year' => fake()->numberBetween(1600, 2010),
            'municipality' => fake()->city(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'region_id' => fake()->randomElement($regionIds),
            'importance' => fake()->numberBetween(1, 10),
            'perex' => fake()->sentences(2, true),
            'description' => fake()->sentences(4, true),
            'user_id' => User::factory(),
        ];
    }
    
    public function isWorkshop(): static
    {
        return $this->state(fn() => [
            'is_workshop' => true,
            'workshop_name' => fake()->word()
        ]);
    }

}
