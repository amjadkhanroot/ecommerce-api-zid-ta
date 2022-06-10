<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreSettingSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('store_settings')->insert([
            [
                'key' => 'VAT',
                'type' => 'percentage',
                'value' => '15'
            ]
        ]);
    }
}
