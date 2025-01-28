<?php

namespace Database\Seeders;

use App\Models\About;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AboutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        About::create([
            'title' => 'About Page',
            'details' => 'We stand by your side with empathy, relentless advocacy, and trust. Your fight for justice is our mission, and we’ll support you every step of the way, just like a good friend would.',
            'image' => 'uploads/images/aboutImage/1822037096365359.webp'
        ]);
    }
}
