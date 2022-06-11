<?php

namespace App\Http\Controllers\API\Store;



use App\Http\Traits\ApiResponseTrait;
use App\Models\SellerDetails;
use Illuminate\Http\Request;

class StoreSettingController
{

    use ApiResponseTrait;

    public function setStoreSetting(Request $request){
        $requestData = $request->validate([
            'shopping_cost' => 'required|numeric|min:0'
        ]);

        SellerDetails::updateShoppingCost($requestData['shopping_cost'], auth()->id());

        return $this->apiResponse(true, 'updated!');
    }
}
