<?php

namespace Database\Seeders;

use App\Models\Home;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HomeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Home::create([
            'name' => 'Home',
            'quote' => 'We Fight For Your Justice Like A Good Friend.',
            'details' => 'When life feels unfair, you need more than just a legal representative—you need someone who cares. We stand by your side with empathy, relentless advocacy, and trust. Your fight for justice is our mission, and we’ll support you every step of the way, just like a good friend would.',
            'image' => 'uploads/images/homeImage/1822037096365359.webp'
        ]);
    }
}
