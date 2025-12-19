<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Category;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::first();

        Event::create([
            'title' => 'Tech Conference',
            'desc' => 'Annual tech event',
            'date' => now()->addDays(30),
            'available_seats' => 100,
            'category_id' => $category->id,
        ]);
    }
}
