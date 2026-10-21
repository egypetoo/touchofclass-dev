<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name_en' => fake()->company(),
            'name_ar' => fake()->company(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Organization $organization) {
            $organization->admin = Admin::factory()->create([
                'organization_id' => $organization->id,
            ]);
        });
    }
}
