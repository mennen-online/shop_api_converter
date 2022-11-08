<?php

namespace Database\Seeders;

use App\Models\EntityField;
use Illuminate\Database\Seeder;

class EntityFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EntityField::factory()
            ->count(5)
            ->createQuietly();
    }
}
