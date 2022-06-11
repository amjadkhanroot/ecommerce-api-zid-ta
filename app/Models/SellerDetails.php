<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerDetails extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','email','name','shopping_cost'];

    public static function getMyProfile($userId)
    {
        return SellerDetails::where('user_id', $userId);
    }

    public function scopeUpdateShoppingCost($query, $shoppingCost, $userId)
    {
        return $query->where('user_id', $userId)->update(['user_id' => $userId, 'shopping_cost' => $shoppingCost]);
    }
}
