<?php

namespace Database\Factories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media>
 */
class MediaFactory extends Factory
{
    protected $model = Media::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'filename' => 'random.jpg',
            'path' => 'uploads/' . $this->faker->uuid . '.jpg',
            'type' => $this->faker->randomElement(['image', 'video']),
            'order' => $this->faker->randomDigitNotNull(),
        ];
    }
}
