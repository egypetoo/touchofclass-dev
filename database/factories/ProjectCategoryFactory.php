<?php

namespace Database\Factories;

use App\Models\ProjectCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ProjectCategory>
 */
class ProjectCategoryFactory extends Factory
{
    protected $model = ProjectCategory::class;

    public function definition(): array
    {
        $nameEn = fake()->unique()->words(2, true);

        return [
            'name' => [
                'en' => Str::title($nameEn),
                'ar' => 'تصنيف '.fake()->unique()->numerify('##'),
            ],
            'slug' => [
                'en' => Str::slug($nameEn),
                'ar' => Str::slug($nameEn),
            ],
            'icon' => 'fas fa-futbol',
            'order' => fake()->numberBetween(1, 20),
            'is_active' => true,
        ];
    }
}
