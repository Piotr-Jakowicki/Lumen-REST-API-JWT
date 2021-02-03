<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\CategoryImage;
use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CategoryImage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'category_id' => $this->faker->randomElement(Category::all()->pluck('id')),
            'image_id' => $this->faker->randomElement(Image::all()->pluck('id')),
        ];
    }
}
