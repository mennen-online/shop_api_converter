<?php

namespace Database\Seeders;

use App\Models\Endpoint;
use Illuminate\Database\Seeder;

class EndpointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Endpoint::factory()
            ->count(5)
            ->create();
    }
}
