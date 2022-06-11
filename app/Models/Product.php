<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable = [
        'user_id',
        'active',
        'sku',
        'inventory',
        'manufacture',
        'image',
        'thumbnail',
        'name',
        'description',
        'price',
        'currency',
        'is_vat_included'
    ];

    public $translatable = [
        'name',
        'description',
        'price',
        'currency'
    ];

    protected $hidden = ['user_id', 'created_at', 'updated_at'];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function scopeMine($query,$userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public static function existForUser($sku, $userId): bool
    {
        return Product::where([['sku', $sku], ['user_id', $userId]])->first() != null;
    }

    public static function uploadImage(Request $request, $product): void
    {
        if ($request->has('image')) {
            if (Storage::exists('public/' . $product->image)) {
                Storage::delete('public/' . $product->image);
                Storage::delete('public/'  . $product->thumbnail);
            }

            $image = $request->file('image');
            $fileName = $image->hashName();
            $path = storage_path('app/public/product_images/');

            $thumbnail = Image::make($request->file('image'))->resize(100, 100);

            $imageName = 'img_' . $fileName;
            $thumbnailName =  'thumb_' . $fileName;
            $image->move($path, $imageName);
            $thumbnail->save($path . $thumbnailName, 80);

            $product->image = 'product_images/'.$imageName;
            $product->thumbnail = 'product_images/'.$thumbnailName;
            $product->save();
        }
    }

     public function  getImageAttribute($image): string
     {
         return config('app.url').Storage::url($image);
     }

    public function  getThumbnailAttribute($thumbnail): string
    {
        return config('app.url').Storage::url($thumbnail);
    }

}
