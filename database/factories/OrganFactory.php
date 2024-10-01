<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\Region;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrganBuilder>
 */
class OrganFactory extends Factory
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
            'place' => fake()->words(3, true),
            'municipality' => fake()->city(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'region_id' => fake()->randomElement($regionIds),
            'importance' => fake()->numberBetween(1, 10),
            //'organ_builder_id' => 
            'year_built' => fake()->numberBetween(1600, 2010),
            'manuals_count' => fake()->numberBetween(1, 5),
            'stops_count' => fake()->numberBetween(5, 60),
            'perex' => fake()->sentences(2, true),
            'description' => fake()->sentences(4, true),
            'user_id' => User::factory(),
        ];
    }

}
