<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Tag;
use App\Models\Link;
use App\Models\Media;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        // Create 10 tags
        $tags = Tag::factory()->count(10)->create();

        // Create 5 projects
        Project::factory()->count(5)->create()->each(function ($project) use ($tags) {
            // Attach 2-5 random tags to each project
            $project->tags()->syncWithPivotValues(
                $tags->random(rand(2, 5))->pluck('id'),
                ['order' => rand(1, 10)]
            );

            // Create 2-4 links per project
            Link::factory()->count(rand(2, 4))->create([
                'project_id' => $project->id,
            ]);

            // Create 3-6 media items per project
            Media::factory()->count(rand(3, 6))->create([
                'project_id' => $project->id,
            ]);
        });
    }
}
