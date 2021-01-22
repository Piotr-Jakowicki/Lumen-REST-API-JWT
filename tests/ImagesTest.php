<?php

use App\Http\Controllers\Api\ImageController;
use App\Models\Image;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Storage;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ImagesTest extends TestCase
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
        // Image factory require at least 1 user
        User::factory()->create();

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
        // Image factory require at least 1 user
        User::factory()->create();

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
        // Image factory require at least 1 user
        User::factory()->create();

        Image::factory()->create(['title' => 'a']);
        Image::factory()->create(['title' => 'b']);
        Image::factory()->create(['title' => 'c']);

        $this->get('/api/images?desc');

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
        // Image factory require at least 1 user
        User::factory()->create();

        $image = Image::factory()->create();

        $this->get("/api/images/$image->id");

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
                'id' => $image->id,
                'user_id' => $image->user_id,
                'title' => $image->title,
                'path' => $image->path
            ]
        ]);
    }

    /**
     * @test
     */
    public function should_return_error_if_image_does_not_exist_in_show_method()
    {
        // Image factory require at least 1 user
        User::factory()->create();

        $image = Image::factory()->create();

        $this->get('/api/images/-1');

        $this->assertEquals(404, $this->response->status());
        $this->seeJsonStructure([
            'error'
        ]);
        $this->seeJson([
            'error' => 'Image not found!'
        ]);
    }

    /**
     * @test
     */
    public function should_delete_image()
    {
        $user = User::factory()->create();
        $image = Image::factory()->create();

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
                'user_id' => $image->user_id,
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

        Storage::fake('public');

        $this
            ->actingAs($user)
            ->post('/api/images', [
                'title' => 'Image',
                'image' => UploadedFile::fake()->image('img.png')
            ]);

        $this->assertEquals(201, $this->response->status());
        $this->seeJsonStructure([
            'data' => [
                'id',
                'user_id',
                'title',
                'path'
            ]
        ]);
        $this->seeInDatabase('images', ['title' => 'Image']);
    }

    // TODO store valdation test

    /**
     * @test
     * @TODO fix update test >> require _method=PATCH
     */
    public function should_update_image()
    {
        $user = User::factory()->create();
        $image = Image::factory()->create();

        Storage::fake('public');

        // Weird way to pass new image because _method=PATCH as parameter dont work
        $request = $this->createRequest(
            'POST',
            [],
            '/api/images/1',
            [],
            [],
            [],
            ['image' => UploadedFile::fake()->image('img.png')]
        );

        // Directly pass data to ImageController@update
        $response = app()->call('App\Http\Controllers\Api\ImageController@update', ['id' => 1, 'request' => $request]);

        $this->patch('/api/images/1', ['title' => 'new title']);

        $this->assertEquals(200, $this->response->status());
        $this->seeJsonStructure([
            'data' => [
                'title',
                'path'
            ]
        ]);

        $this->seeJson([
            'data' => [
                'id' => 1,
                'title' => 'new title',
                'path' => $response->path,
                'user_id' => $user->id
            ]
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

    protected function createRequest(
        $method,
        $content,
        $uri,
        $server,
        $parameters = [],
        $cookies = [],
        $files = []
    ) {
        $request = new \Illuminate\Http\Request;
        return $request->createFromBase(
            \Symfony\Component\HttpFoundation\Request::create(
                $uri,
                $method,
                $parameters,
                $cookies,
                $files,
                $server,
                $content
            )
        );
    }
}
