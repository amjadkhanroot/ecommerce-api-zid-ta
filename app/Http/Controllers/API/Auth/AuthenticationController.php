<?php

namespace App\Http\Controllers\API\Auth;

use App\Enums\RoleEnum;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Role;
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

        $userInfo = $request->except('store_info', 'role');

        $sellerInfo = [];
        if (isset($validateData['role']) && $validateData['role'] === RoleEnum::get('seller')){
            $sellerInfo = $this->sellerExtraFields($request);
            $userInfo['role_id'] = Role::where('code', $validateData['role'])->first()->id;
        }

        $userInfo['password'] = bcrypt($userInfo['password']);
        $user = User::create($userInfo);

        if (count($sellerInfo) > 0){
            $sellerInfo = $sellerInfo['store_info'][0];
            $sellerInfo['user_id'] = $user->id;

            $user->storeInfo = SellerDetails::create($sellerInfo);
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
            'store_info.0.email' => 'required|string|email:rfc,dns|max:255',
            'store_info.0.name' => 'required|string|min:2|max:255',
            'store_info.0.shopping_cost' => 'required|numeric|min:0'
        ]);
    }
}
