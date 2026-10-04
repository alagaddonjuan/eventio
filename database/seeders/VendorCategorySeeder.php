<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VendorCategory;
use Illuminate\Support\Str;

class VendorCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Venue', 'icon' => 'building'],
            ['name' => 'Catering & Food', 'icon' => 'cake'],
            ['name' => 'Photography & Video', 'icon' => 'camera'],
            ['name' => 'Music & DJ', 'icon' => 'music'],
            ['name' => 'Decor & Rentals', 'icon' => 'sparkles'],
            ['name' => 'Event Planning', 'icon' => 'clipboard-list'],
        ];

        foreach ($categories as $cat) {
            VendorCategory::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                ['name' => $cat['name'], 'icon' => $cat['icon']]
            );
        }
    }
}
