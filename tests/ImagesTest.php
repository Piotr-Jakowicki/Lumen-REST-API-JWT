<?php

use App\Models\Category;
use App\Models\Image;
use App\Models\User;
use App\Requests\Images\UpdateRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Laravel\Lumen\Testing\DatabaseMigrations;


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
    public function should_return_image_categories()
    {
        // Image factory require at least 1 user
        User::factory()->create();
        $categories = Category::factory()->count(3)->create();

        $image = Image::factory()->create();
        $image->categories()->attach([1, 2, 3]);
        $image->load('categories');

        $this->get("/api/images/$image->id");

        $this->assertEquals(200, $this->response->status());
        $this->seeJsonStructure([
            'data' => [
                'id',
                'user_id',
                'title',
                'path',
                'categories'
            ]
        ]);
        $this->seeJson([
            'data' => [
                'id' => $image->id,
                'user_id' => $image->user_id,
                'title' => $image->title,
                'path' => $image->path,
                'categories' => [
                    [
                        'id' => 1,
                        'name' => $categories[0]->name,
                        'parent_id' => $categories[0]->parent_id
                    ],
                    [
                        'id' => 2,
                        'name' => $categories[1]->name,
                        'parent_id' => $categories[1]->parent_id
                    ],
                    [
                        'id' => 3,
                        'name' => $categories[2]->name,
                        'parent_id' => $categories[2]->parent_id
                    ],
                ]
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

    /**
     * @test
     */
    public function image_store_validation_form()
    {
        $this->post('/api/images', [
            'image' => '',
            'title' => ''
        ]);

        $this->assertEquals(422, $this->response->status());
        $this->seeJsonStructure([
            'image',
            'title'
        ]);
    }

    /**
     * @test
     */
    public function image_update_validation_form()
    {
        $this->post('/api/images', [
            'image' => '',
            'title' => ''
        ]);

        $this->assertEquals(422, $this->response->status());
        $this->seeJsonStructure([
            'image',
            'title'
        ]);
    }

    /**
     * @test
     */
    public function should_update_image()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $image = Image::factory()->create();

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

        $request = new UpdateRequest($request);

        // Directly pass new request instance to ImageContoller@update
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

    /**
     * @test
     */
    public function should_store_image_with_categories()
    {
        $user = User::factory()->create();
        Category::factory()->count(3)->create();

        Storage::fake('public');

        $this
            ->actingAs($user)
            ->post('/api/images', [
                'title' => 'Image',
                'image' => UploadedFile::fake()->image('img.png'),
                'categories' => [1, 2, 3]
            ]);

        $this->assertEquals(201, $this->response->status());
        $this->seeJsonStructure([
            'data' => [
                'id',
                'user_id',
                'title',
                'path',
                'categories' => [
                    '*' => [
                        'id',
                        'parent_id',
                        'name'
                    ]
                ]
            ]
        ]);
        $this->seeInDatabase('images', ['title' => 'Image']);
        $this->seeInDatabase('category_image', ['image_id' => '1']);
    }

    /**
     * @test
     */
    // public function images_list_cache()
    // {
    //     Cache::shouldReceive('tags')
    //         ->once()
    //         ->with('images')
    //         ->andReturnSelf();

    //     Cache::shouldReceive('remember')
    //         ->once()
    //         ->with('images_', 1800, Closure::class)
    //         ->andReturn();

    //     $this->get('/api/images');
    // }

    /**
     * @test
     */
    // public function images_list_cache_with_parameter()
    // {
    //     Cache::shouldReceive('tags')
    //         ->once()
    //         ->with('images')
    //         ->andReturnSelf();

    //     Cache::shouldReceive('remember')
    //         ->once()
    //         ->with('images_limit=50', 1800, Closure::class)
    //         ->andReturn();

    //     $this->get('/api/images?limit=50');
    // }

    /**
     * @test
     */
    // public function images_list_cache_with_parameters_and_sort()
    // {
    //     Cache::shouldReceive('tags')
    //         ->once()
    //         ->with('images')
    //         ->andReturnSelf();

    //     Cache::shouldReceive('remember')
    //         ->once()
    //         ->with('images_a=1_limit=50', 1800, Closure::class)
    //         ->andReturn();

    //     $this->get('/api/images?limit=50&a=1');
    // }

    /**
     * @test
     */
    // public function images_find_cache()
    // {
    //     User::factory()->create();
    //     $image = Image::factory()->create();

    //     Cache::shouldReceive('tags')
    //         ->once()
    //         ->with("images_$image->id")
    //         ->andReturnSelf();

    //     Cache::shouldReceive('remember')
    //         ->once()
    //         ->with("images_$image->id", 1800, Closure::class)
    //         ->andReturn();

    //     $this->get("/api/images/$image->id");
    // }

    /**
     * @test
     */
    // public function images_store_cache()
    // {
    //     $user = User::factory()->create();
    //     $image = Image::factory()->create();

    //     Cache::shouldReceive('tags')
    //         ->once()
    //         ->with("images")
    //         ->andReturnSelf();

    //     Cache::shouldReceive('flush')
    //         ->once();

    //     $this
    //         ->actingAs($user)
    //         ->post('/api/images', [
    //             'title' => 'Image',
    //             'image' => UploadedFile::fake()->image('img.png')
    //         ]);
    // }

    /**
     * @test
     */
    // public function images_update_cache()
    // {
    //     $user = User::factory()->create();
    //     $image = Image::factory()->create();

    //     Cache::shouldReceive('tags')
    //         ->once()
    //         ->with(["images_$image->id", 'images'])
    //         ->andReturnSelf();

    //     Cache::shouldReceive('flush')
    //         ->once();

    //     $this
    //         ->actingAs($user)
    //         ->patch("/api/images/$image->id", [
    //             'title' => 'Image',
    //         ]);
    // }

    /**
     * @test
     */
    // public function images_delete_cache()
    // {
    //     $user = User::factory()->create();
    //     $image = Image::factory()->create();

    //     Cache::shouldReceive('tags')
    //         ->once()
    //         ->with(["images_$image->id", 'images'])
    //         ->andReturnSelf();

    //     Cache::shouldReceive('flush')
    //         ->once();

    //     $this
    //         ->actingAs($user)
    //         ->delete("/api/images/$image->id");
    // }

    /**
     * @test
     */
    public function should_create_image_with_categories()
    {
        $user = User::factory()->create();
        Category::factory()->count(2)->create();

        Storage::fake('public');

        $this
            ->actingAs($user)
            ->post('/api/images', [
                'title' => 'Image',
                'image' => UploadedFile::fake()->image('img.png'),
                'categories[0]' => 1,
                'categories[1]' => 2,
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
