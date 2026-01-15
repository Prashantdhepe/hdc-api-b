<?php

namespace Database\Seeders;

use App\Models\SchoolType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SchoolType::create([
            'name' => 'HDC Kids, A Play School',
            'slug' => 'hdc-kids'
        ]);

        SchoolType::create([
            'name' => 'HDC International',
            'slug' => 'hdc-international'
        ]);
    }
}
