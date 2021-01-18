<?php

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function list_images()
    {
        Image::factory()->count(10)->make();

        $this->get('/api/images');

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
    public function list_can_be_filtered_by_title()
    {
        Image::factory()->count(10)->create(['title' => 'false']);
        Image::factory()->count(2)->create(['title' => 'true']);

        $this->get('/api/images?title=true');

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
        $this->assertEquals(2, count(json_decode($this->response->getContent())->data));
    }

    /**
     * @test
     */
    public function list_can_be_sorted_asc_by_title()
    {
        Image::factory()->create(['title' => 'c']);
        Image::factory()->create(['title' => 'b']);
        Image::factory()->create(['title' => 'a']);

        $this->get('/api/images?asc');

        $this->assertEquals(200, $this->response->status());
        $response = (json_decode($this->response->getContent())->data);

        foreach ($response as $row) {
            $order[] = $row->title;
        }

        $this->assertEquals(['a', 'b', 'c'], $order);
    }

    /**
     * @test
     */
    public function list_can_be_sorted_desc_by_title()
    {
        Image::factory()->create(['title' => 'c']);
        Image::factory()->create(['title' => 'b']);
        Image::factory()->create(['title' => 'a']);

        $this->get('/api/images?asc');

        $this->assertEquals(200, $this->response->status());
        $response = (json_decode($this->response->getContent())->data);

        foreach ($response as $row) {
            $order[] = $row->title;
        }

        $this->assertEquals(['c', 'b', 'a'], $order);
    }

    /**
     * @test
     */
    public function should_return_specified_category()
    {
        $category = Image::factory()->create();

        $this->get("/api/images/$category->id");

        $this->assertEquals(200, $this->response->status());
        $this->seeJsonStructure([
            'data' => [
                'id',
                'user_id',
                'title',
                'path'
            ]
        ]);
        $this->seeJson([
            'data' => [
                'id' => $category->id,
                'parent_id' => $category->parent_id,
                'name' => $category->name
            ]
        ]);
    }

    /**
     * @test
     */
    public function should_return_error_if_image_does_not_exist_in_show_method()
    {
        $category = Image::factory()->create();

        $this->get('/api/images/-1');

        $this->assertEquals(404, $this->response->status());
        $this->seeJsonStructure([
            'error'
        ]);
        $this->seeJson([
            'error' => 'Category not found!'
        ]);
    }

    public function should_delete_image()
    {
        $image = Image::factory()->create();
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->delete("/api/images/$image->id");

        $this->assertEquals(200, $this->response->status());
        $this->seeJsonStructure([
            'data' => [
                'id',
                'user_id',
                'path',
                'title'
            ]
        ]);
        $this->seeJson([
            'data' => [
                'id' => $image->id,
                'user_id' => $user->user_id,
                'path' => $image->path,
                'title' => $image->title
            ]
        ]);
        $this->notSeeInDatabase('images', ['path' => $image->path]);
    }

    /**
     * @test
     */
    public function should_create_image()
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->post('/api/categories', [
                'title' => 'Image'
            ]);

        $this->assertEquals(201, $this->response->status());
        $this->seeJson([
            'data' => [
                'id' => 1,
                'parent_id' => null,
                'title' => 'Image'
            ]
        ]);
        $this->seeInDatabase('categories', ['title' => 'Image']);
    }

    // TODO store valdation test

    /**
     * @test
     */
    public function should_update_image()
    {
        $image = Image::factory()->create();
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->patch("/api/images/$image->id", [
                'title' => "$image->title-updated",
                'path' => "$image->path-updated"
            ]);

        $this->assertEquals(200, $this->response->status());
        $this->seeJson([
            'id' => $image->id,
            'title' => "$image->title-updated",
            'user_id' => $image->user_id
        ]);
    }

    /**
     * @test
     */
    public function return_error_if_user_id_does_not_exists()
    {
        $category = Category::factory()->create();
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->patch("/api/categories/$category->id", [
                'parent_id' => -1
            ]);

        $this->assertEquals(422, $this->response->status());
        $this->seeJsonStructure([
            'parent_id'
        ]);
    }

    /**
     * @test
     */
    public function return_validation_error_if_image_model_does_not_exists_in_update_method()
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->patch('/api/images/-1', [
                'title' => 'title'
            ]);

        $this->assertEquals(404, $this->response->status());
        $this->seeJsonStructure([
            'error'
        ]);
    }
}
