<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'active' => $this->faker->randomElement([1,1,1,1,1,1,1,0]),
            'sku' => $this->faker->unique()->countryCode,
            'inventory' => $this->faker->randomNumber(3),
            'manufacture' => $this->faker->company,
            'image' => 'public/default_images/default_product_image.png',
            'thumbnail' => 'public/default_images/default_product_image.png',
            'name'=> ['en' => $this->faker->unique()->firstNameFemale, 'ar' => "تجربة!"],
            'description' => ['en' => $this->faker->realText, 'ar' => "تجربة!"],
            'price' => ['en' => $this->faker->randomNumber(3), 'ar' => $this->faker->randomNumber(3)],
            'currency' => ['en' => 'USD', 'ar' => "SAR"],
            'is_vat_included' => $this->faker->randomElement([1,0]),
            'user_id' => $this->faker->randomElement(User::where('role_id', 2)->get())
        ];
    }
}
