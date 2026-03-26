<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Part;

class PartSeeder extends Seeder
{
    public function run(): void
    {
        $parts = [
            [
                'name' => 'Shimano Deore Derailleur',
                'category' => 'derailer',
                'description' => 'Reliable 12-speed rear derailleur for trail riding.',
                'image' => 'https://picsum.photos/seed/derailer1/400/300',
            ],
            [
                'name' => 'SRAM GX Eagle Derailleur',
                'category' => 'derailer',
                'description' => 'Lightweight 12-speed derailleur with wide range.',
                'image' => 'https://picsum.photos/seed/derailer2/400/300',
            ],
            [
                'name' => 'Renthal Fatbar Handlebars',
                'category' => 'handlebars',
                'description' => 'Wide 800mm aluminum bar for aggressive trail riding.',
                'image' => 'https://picsum.photos/seed/handlebar1/400/300',
            ],
            [
                'name' => 'Specialized Hover Handlebars',
                'category' => 'handlebars',
                'description' => 'Ergonomic carbon bar with vibration damping.',
                'image' => 'https://picsum.photos/seed/handlebar2/400/300',
            ],
            [
                'name' => 'Shimano XT Crankset',
                'category' => 'kranks',
                'description' => 'Stiff and lightweight 175mm crankset.',
                'image' => 'https://picsum.photos/seed/krank1/400/300',
            ],
            [
                'name' => 'SRAM DUB Crankset',
                'category' => 'kranks',
                'description' => 'Wide stance DUB spindle crankset for better power transfer.',
                'image' => 'https://picsum.photos/seed/krank2/400/300',
            ],
            [
                'name' => 'RockShox Pike Fork',
                'category' => 'suspension',
                'description' => '140mm travel fork with Charger 3 damper.',
                'image' => 'https://picsum.photos/seed/suspension1/400/300',
            ],
            [
                'name' => 'Fox 36 Factory Fork',
                'category' => 'suspension',
                'description' => 'Premium 160mm fork with GRIP2 damper.',
                'image' => 'https://picsum.photos/seed/suspension2/400/300',
            ],
        ];

        foreach ($parts as $part) {
            Part::create($part);
        }
    }
}