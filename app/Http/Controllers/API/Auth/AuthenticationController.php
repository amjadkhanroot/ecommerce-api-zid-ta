<?php

namespace App\Http\Controllers\API\Auth;

use App\Enums\RoleEnum;
use App\Http\Traits\ApiResponseTrait;
use App\Models\SellerDetails;
use App\Models\User;
use Illuminate\Http\Request;

class AuthenticationController
{
    use ApiResponseTrait;

    public function register(Request $request)
    {

        $validateData = $request->validate([
            'username' => 'required|string|max:12|unique:users',
            'email' => 'required|string|email:rfc,dns|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|exists:roles,code'
        ]);


        $sellerInfo = [];
        if ($validateData['role'] === RoleEnum::get('seller'))
            $sellerInfo = $this->sellerExtraFields($request);

        $userInfo = $request->except('store_info');
        $userInfo['password'] = bcrypt($userInfo['password']);
        $user = User::create($userInfo);

        if (isset($validateData['store_info'])){
            $sellerInfo = $sellerInfo['store_info'];
            $sellerInfo['user_id'] = $user->id;
            SellerDetails::create($sellerInfo);
        }

        $user->api_token = $user->createToken('Personal Access Token', [])->plainTextToken;

        return $this->apiResponse( true, "success", $user);
    }

    public function login(Request $request)
    {
        $validateData = $request->validate([
            'email' => 'required|string|max:255',
            'password' => 'required',
        ]);

        if (auth()->attempt($validateData)) {
            $user = User::where('email',$validateData['email'])->get()->first();

            //todo: return seller details if seller.
//            if ($user->role->code === RoleEnum::get('seller'))
//                $user->sellerInfo = $this->sellerExtraFields($request);


            $user->api_token = $user->createToken('Personal Access Token', [])->plainTextToken;
            return $this->apiResponse( true, "success", $user);
        }
        return $this->apiResponse( false, "wrong attempts!", [], [], 400);
    }

    private function sellerExtraFields(Request $request): array{
        return $request->validate([
            'store_info' => 'required|array',
            'store_info.*.email' => 'required|string|email:rfc,dns|max:255|unique:store_details',
            'store_info.*.name' => 'required|string|min:2|max:255',
            'store_info.*.shopping_cost' => 'required|numeric|min:0'
        ]);
    }
}
