<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * php artisan migrate:fresh --seed
     * then: php artisan recommendations:train
     */
    public function run()
    {
        $this->call([
            StoreSeeder::class,
            CatalogSeeder::class,
            ShopperSimulationSeeder::class,
        ]);
    }
}
