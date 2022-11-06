<?php

namespace Database\Seeders;

use App\Models\ShopData;
use Illuminate\Database\Seeder;

class ShopDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ShopData::factory()
            ->count(5)
            ->create();
    }
}
