<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SellerDetailsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sellers = User::where('role_id', 2)->get();

        $shoppingCost = [0.0,15.0,7.0,9.0];
        $sellerDetails = [];
        foreach ($sellers as $seller){
            $sellerDetails[] = [
                'user_id' => $seller->id,
                'email' => $seller->email,
                'name' => $seller->username,
                'shopping_cost' => $shoppingCost[array_rand($shoppingCost)]
            ];
         }

        DB::table('seller_details')->insert($sellerDetails);
    }
}
