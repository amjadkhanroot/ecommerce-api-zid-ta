<?php

namespace App\Http\Controllers\API\App;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponseTrait;

    public function list(Request $request){

        $user = User::find(auth()->id());

        $perPage = $this->managePageNLocala($request);

        if (isset($user) && $user->role->code === RoleEnum::get('seller')){
            return $this->apiResponse( true, "success", Product::mine($user->id)->paginate($perPage));
        }else{
            return $this->apiResponse( true, "success", Product::active()->paginate($perPage));
        }
    }

    public function create(Request $request)
    {

        $requestData = $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|max:255',
            'name.ar' => array('required','max:255'),
            'description' => 'required|array',
            'description.en' => 'required|max:255',
            'description.ar' => array('required','max:255'),
            'price' => 'required|array',
            'price.en' => 'required|numeric',
            'price.ar' => array('required','numeric'),

            'manufacture' => 'required|max:255',
            'sku' => 'required|max:255',
            'inventory' => 'required|max:255',
            'is_vat_included' => 'required|bool',
            'is_active' => 'required|bool',
            'image' => 'required|image|mimes:png,jpg,jpeg|max:3500|dimensions:width=500,height=500'
        ]);

        dd(json_encode($request->post()));

        if (Product::existForUser($requestData['sku'], auth()->id()))
            return $this->apiResponse( false, "SKU already exists!", [], [], 422);


        $productDetails = $request->except('image');
        $productDetails['currency'] = ['en' => 'USD', 'ar' => 'SAR']; //demo purpose.
        $productDetails['user_id'] = auth()->id();
        $product = Product::create($productDetails);

        Product::uploadImage($request, $product); //for demo purpose only, one image for one product.

        return $this->apiResponse( true, "success", Product::find($product->id));
    }

    /**
     * @param Request $request
     * @return array|int|string|null
     */
    private function managePageNLocala(Request $request): string|int|array|null
    {
        $perPage = 6;
        if ($request->query('per_page') !== null)
            $perPage = $request->query('per_page');

        $locale = 'en';
        if ($request->query('locale') !== null)
            $locale = $request->query('locale');

        app()->setLocale($locale);
        return $perPage;
    }
}
