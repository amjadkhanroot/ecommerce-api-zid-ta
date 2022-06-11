<?php

namespace App\Http\Controllers\API\App;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Cart;
use App\Models\Product;
use App\Models\SellerDetails;
use App\Models\StoreSetting;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiResponseTrait;

    public function getMyCart(Request $request){

        $cartDetails = Cart::with('product')->where('user_id', auth()->id())->get();

        $this->managePageNLocala($request);
        $vat = StoreSetting::getVat()->first();
        $subtotal = 0.0;
        $totalVat = 0.0;
        $totalShoppingCost = 0.0;
        $currency = 'USD'; //default fallback
        $response = [];
        $items = [];
        $shoppingCostPerSeller = [];

        list($currency, $subtotal, $totalVat, $items, $totalShoppingCost) = $this->calculateItemsBreakdown($cartDetails, $currency, $subtotal, $vat, $totalVat, $items, $shoppingCostPerSeller, $totalShoppingCost);

        $response = $this->buildResponse($items, $response, $subtotal, $totalVat, $vat, $totalShoppingCost, $currency);

        return $this->apiResponse(true, 'success!', $response);
    }

    public function addToCart(Request $request){
        $requestData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
        ]);

        $product = Product::where([['active', true], ['id', $requestData['product_id'], ['inventory' >= $requestData['quantity']]]])->first();

        if (!isset($product))
            return $this->apiResponse(false, 'we can not provide this amount at the moment please consider lowering the quantity to the available!', [], ['quantity' => 'above the maximum allowed!'], 422);

        $requestData['user_id'] = auth()->id();
        Cart::create($requestData);

        return $this->apiResponse(true, 'added!');
    }

    public function removeFromCart(Request $request){
        $requestData = $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        Cart::where([['user_id', auth()->id()], ['product_id', $requestData['product_id']]])->delete();

        return $this->apiResponse(true, 'removed!');

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

    /**
     * @param \Illuminate\Database\Eloquent\Collection|array $cartDetails
     * @param $currency
     * @param mixed $subtotal
     * @param $vat
     * @param mixed $totalVat
     * @param array $items
     * @param array $shoppingCostPerSeller
     * @param mixed $totalShoppingCost
     * @return array
     */
    private function calculateItemsBreakdown(\Illuminate\Database\Eloquent\Collection|array $cartDetails, $currency, mixed $subtotal, $vat, mixed $totalVat, array $items, array $shoppingCostPerSeller, mixed $totalShoppingCost): array
    {
        foreach ($cartDetails as $item) {

            $product = Product::find($item->product_id);

            $currency = $product->currency; //demo purpose.
            $itemVat = 0.0;
            $totalQuantityPrice = $product->price * $item->quantity;
            $sellerInfo = SellerDetails::getMyProfile($product->user_id)->first();

            if ($product->is_vat_included) {
                $subtotal = $subtotal + $totalQuantityPrice;
            } else {
                $itemVat = $totalQuantityPrice * ($vat->value / 100);
                $totalVat = $totalVat + ($totalQuantityPrice * ($vat->value / 100));
                $subtotal = $subtotal + ($totalQuantityPrice * (($vat->value / 100) + 1));
            }

            $items[] = [
                'item' => $product,
                'quantity' => $item->quantity,
                'total_vat' => $itemVat,
                'shopping_cost' => $sellerInfo->shopping_cost,
            ];

            if (!isset($shoppingCostPerSeller[$sellerInfo->id])) {
                $shoppingCostPerSeller[$sellerInfo->id] = $sellerInfo->shopping_cost;
                $totalShoppingCost = $totalShoppingCost + $sellerInfo->shopping_cost;
            }
        }
        return array($currency, $subtotal, $totalVat, $items, $totalShoppingCost);
    }

    /**
     * @param mixed $items
     * @param array $response
     * @param mixed $subtotal
     * @param mixed $totalVat
     * @param $vat
     * @param mixed $totalShoppingCost
     * @param mixed $currency
     * @return array
     */
    private function buildResponse(mixed $items, array $response, mixed $subtotal, mixed $totalVat, $vat, mixed $totalShoppingCost, mixed $currency): array
    {
        $response['items'] = $items;
        $response['subtotal'] = round($subtotal, 2);
        $response['total_vat'] = round($totalVat, 2);
        $response['VAT'] = $vat->value . '%';
        $response['total_shopping_cost'] = round($totalShoppingCost, 2);
        $response['total'] = round($subtotal + $totalShoppingCost, 2);
        $response['currency'] = $currency;
        return $response;
    }
}
