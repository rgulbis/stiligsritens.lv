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
            // Derailers - 12-speed
            [
                'name' => 'Shimano Deore Derailleur',
                'category' => 'derailer',
                'description' => 'Reliable 12-speed rear derailleur for trail riding.',
                'image' => 'https://picsum.photos/seed/derailer1/400/300',
                'compatibility' => [
                    'min_speeds' => 12,
                    'max_speeds' => 12,
                    'frame_types' => ['mountain', 'hybrid', 'trail'],
                ],
            ],
            [
                'name' => 'SRAM GX Eagle Derailleur',
                'category' => 'derailer',
                'description' => 'Lightweight 12-speed derailleur with wide range.',
                'image' => 'https://picsum.photos/seed/derailer2/400/300',
                'compatibility' => [
                    'min_speeds' => 12,
                    'max_speeds' => 12,
                    'frame_types' => ['mountain', 'trail'],
                ],
            ],
            // Derailers - 7-8 speed (incompatible with 12-speed)
            [
                'name' => 'Shimano Altus Derailleur',
                'category' => 'derailer',
                'description' => 'Budget-friendly 7-8 speed derailleur for casual riding.',
                'image' => 'https://picsum.photos/seed/derailer3/400/300',
                'compatibility' => [
                    'min_speeds' => 7,
                    'max_speeds' => 8,
                    'frame_types' => ['mountain', 'hybrid', 'casual'],
                ],
            ],
            // Cassettes - 12-speed
            [
                'name' => 'Shimano XT Cassette 12-speed',
                'category' => 'cassette',
                'description' => '12-speed cassette with 10-51T range.',
                'image' => 'https://picsum.photos/seed/cassette1/400/300',
                'compatibility' => [
                    'speeds' => 12,
                    'frame_types' => ['mountain', 'hybrid', 'trail'],
                ],
            ],
            [
                'name' => 'SRAM GX Eagle Cassette',
                'category' => 'cassette',
                'description' => '12-speed cassette with massive 10-52T range.',
                'image' => 'https://picsum.photos/seed/cassette2/400/300',
                'compatibility' => [
                    'speeds' => 12,
                    'frame_types' => ['mountain', 'trail', 'enduro'],
                ],
            ],
            // Cassettes - 8-speed
            [
                'name' => 'Shimano HG31 Cassette',
                'category' => 'cassette',
                'description' => 'Durable 8-speed cassette 11-34T.',
                'image' => 'https://picsum.photos/seed/cassette3/400/300',
                'compatibility' => [
                    'speeds' => 8,
                    'frame_types' => ['mountain', 'hybrid', 'casual'],
                ],
            ],
            // Handlebars
            [
                'name' => 'Renthal Fatbar Handlebars',
                'category' => 'handlebars',
                'description' => 'Wide 800mm aluminum bar for aggressive trail riding.',
                'image' => 'https://picsum.photos/seed/handlebar1/400/300',
                'compatibility' => [
                    'frame_types' => ['mountain', 'trail'],
                ],
            ],
            [
                'name' => 'Specialized Hover Handlebars',
                'category' => 'handlebars',
                'description' => 'Ergonomic carbon bar with vibration damping.',
                'image' => 'https://picsum.photos/seed/handlebar2/400/300',
                'compatibility' => [
                    'frame_types' => ['road', 'hybrid', 'mountain'],
                ],
            ],
            // Cranks
            [
                'name' => 'Shimano XT Crankset',
                'category' => 'crankset',
                'description' => 'Stiff and lightweight 175mm crankset.',
                'image' => 'https://picsum.photos/seed/krank1/400/300',
                'compatibility' => [
                    'frame_types' => ['mountain', 'trail', 'hybrid'],
                ],
            ],
            [
                'name' => 'SRAM DUB Crankset',
                'category' => 'crankset',
                'description' => 'Wide stance DUB spindle crankset for better power transfer.',
                'image' => 'https://picsum.photos/seed/krank2/400/300',
                'compatibility' => [
                    'frame_types' => ['mountain', 'trail', 'enduro'],
                ],
            ],
            // Suspension
            [
                'name' => 'RockShox Pike Fork',
                'category' => 'fork',
                'description' => '140mm travel fork with Charger 3 damper.',
                'image' => 'https://picsum.photos/seed/suspension1/400/300',
                'compatibility' => [
                    'frame_types' => ['mountain', 'trail', 'hardtail'],
                ],
            ],
            [
                'name' => 'Fox 36 Factory Fork',
                'category' => 'fork',
                'description' => 'Premium 160mm fork with GRIP2 damper.',
                'image' => 'https://picsum.photos/seed/suspension2/400/300',
                'compatibility' => [
                    'frame_types' => ['mountain', 'enduro', 'trail'],
                ],
            ],
            // Frames
            [
                'name' => 'Trek Slash 9.9',
                'category' => 'frame',
                'description' => 'Full-suspension trail bike frame.',
                'image' => 'https://picsum.photos/seed/frame1/400/300',
                'compatibility' => [
                    'type' => 'trail',
                    'min_fork_travel' => 140,
                ],
            ],
            [
                'name' => 'Santa Cruz Bronson',
                'category' => 'frame',
                'description' => 'Enduro-capable full suspension frame.',
                'image' => 'https://picsum.photos/seed/frame2/400/300',
                'compatibility' => [
                    'type' => 'enduro',
                    'min_fork_travel' => 150,
                ],
            ],
            [
                'name' => 'Specialized Hardtail Comp',
                'category' => 'frame',
                'description' => 'Rigid hardtail mountain bike frame.',
                'image' => 'https://picsum.photos/seed/frame3/400/300',
                'compatibility' => [
                    'type' => 'hardtail',
                    'min_fork_travel' => 100,
                ],
            ],
            // Tires
            [
                'name' => 'Maxxis Minion DHF',
                'category' => 'tire',
                'description' => 'Aggressive trail tire with excellent traction.',
                'image' => 'https://picsum.photos/seed/tire1/400/300',
                'compatibility' => [
                    'frame_types' => ['mountain', 'trail', 'enduro'],
                ],
            ],
            [
                'name' => 'Schwalbe Magic Mary',
                'category' => 'tire',
                'description' => 'Downhill-focused tire for wet and loose conditions.',
                'image' => 'https://picsum.photos/seed/tire2/400/300',
                'compatibility' => [
                    'frame_types' => ['enduro', 'downhill'],
                ],
            ],
        ];

        foreach ($parts as $part) {
            Part::create($part);
        }
    }
}