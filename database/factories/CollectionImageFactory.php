<?php

namespace Database\Factories;

use App\Models\Collection;
use App\Models\CollectionImage;
use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;

class CollectionImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CollectionImage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'collection_id' => $this->faker->randomElement(Collection::all()->pluck('id')),
            'image_id' => $this->faker->randomElement(Image::all()->pluck('id')),
        ];
    }
}
