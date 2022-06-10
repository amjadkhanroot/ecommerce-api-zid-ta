<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            [
                 'name' => 'Customer',
                 'code' => RoleEnum::get('customer')
            ],
            [
                'name' => 'Seller',
                'code' => RoleEnum::get('seller')
            ]
        ]);
    }
}
