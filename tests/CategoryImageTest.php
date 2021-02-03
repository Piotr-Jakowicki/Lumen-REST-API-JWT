<?php

use App\Models\Category;
use App\Models\Image;
use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;

class CategoryImageTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * @test
     */

    public function should_return_category_images()
    {
        User::factory()->create();
        $category = Category::factory()->hasImages(5)->create();

        $this->get("/api/categories/$category->id/images");

        $this->assertEquals(200, $this->response->status());
        $this->seeJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'path',
                    'title'
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function category_image_is_filterable_by_title()
    {
        $user = User::factory()->create();

        $category = Category::factory()
            ->hasImages(5)
            ->create();

        $image = Image::factory()
            ->hasAttached($category)
            ->create(['title' => 'test']);

        $this->get("/api/categories/$category->id/images?title=test");

        $this->assertEquals(200, $this->response->status());
        $this->seeJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'path',
                    'title'
                ]
            ]
        ]);
        $this->seeJson([
            'id' => $image->id,
            'user_id' => $user->id,
            'path' => $image->path,
            'title' => $image->title,
        ]);
    }

    /**
     * @test
     */
    public function category_image_is_sortable_asc()
    {
        User::factory()->create();

        $category = Category::factory()
            ->create();

        Image::factory()->hasAttached($category)->create(['title' => 'c']);
        Image::factory()->hasAttached($category)->create(['title' => 'b']);
        Image::factory()->hasAttached($category)->create(['title' => 'a']);


        $this->get("/api/categories/$category->id/images?asc");

        $this->assertEquals(200, $this->response->status());
        $this->seeJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'path',
                    'title'
                ]
            ]
        ]);

        $response = (json_decode($this->response->getContent())->data);

        foreach ($response as $row) {
            $order[] = $row->title;
        }

        $this->assertEquals(['a', 'b', 'c'], $order);
    }

    /**
     * @test
     */
    public function category_image_is_sortable_desc()
    {
        User::factory()->create();

        $category = Category::factory()
            ->create();

        Image::factory()->hasAttached($category)->create(['title' => 'a']);
        Image::factory()->hasAttached($category)->create(['title' => 'b']);
        Image::factory()->hasAttached($category)->create(['title' => 'c']);

        $this->get("/api/categories/$category->id/images?desc");

        $this->assertEquals(200, $this->response->status());
        $this->seeJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'path',
                    'title'
                ]
            ]
        ]);

        $response = (json_decode($this->response->getContent())->data);

        foreach ($response as $row) {
            $order[] = $row->title;
        }

        $this->assertEquals(['c', 'b', 'a'], $order);
    }
}
