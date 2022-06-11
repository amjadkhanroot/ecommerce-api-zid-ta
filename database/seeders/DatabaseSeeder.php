<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call(StoreSettingSeed::class);
        $this->call(RoleSeed::class);
        $this->call(UserSeed::class);
        $this->call(SellerDetailsSeed::class);
        $this->call(ProdcutSeed::class);
    }
}
