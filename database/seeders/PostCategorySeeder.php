<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'About',
                'slug' => 'about',
            ],
            [
                'name' => 'Academics',
                'slug' => 'academics',
                'children' => [
                    [
                        'name' => 'Rules and Regulations',
                        'slug' => 'rules-and-regulations',
                    ],
                    [
                        'name' => 'News Room',
                        'slug' => 'news-room',
                    ],
                    [
                        'name' => 'Bus Routes',
                        'slug' => 'bus-routes',
                    ],
                    [
                        'name' => 'Time Tables',
                        'slug' => 'time-tables',
                    ],
                    [
                        'name' => 'School Timings',
                        'slug' => 'school-timings',
                    ],
                    [
                        'name' => 'Mandatory Disclosures',
                        'slug' => 'mandatory-disclosures',
                    ],
                ]
            ],
            [
                'name' => 'Activities',
                'slug' => 'activities',
            ],
            [
                'name' => 'Achievements',
                'slug' => 'achievements',
            ],
        ];
    }
}
