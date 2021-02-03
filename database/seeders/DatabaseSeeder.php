<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryImage;
use App\Models\Image;
use App\Models\User;
use App\Models\Collection;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$this->call('UsersTableSeeder');
        User::factory()->times(10)->create();

        for ($i = 0; $i < 10; $i++) {
            Category::factory()->times(2)->create();
        }

        Image::factory()->times(10)->create();
        Collection::factory()->times(10)->create();
        CategoryImage::factory()->times(30)->create();
    }
}
