<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use App\Models\Collection;
use App\Models\User;

class CollectionsTest extends TestCase
{
    use DatabaseMigrations;

    // Index

    /**
     * @test
     */
    public function list_collections()
    {
        User::factory()->create();
        Collection::factory()->count(10)->create();

        $this->get('/api/collections');

        $this->assertEquals(200, $this->response->status());
        $this->seeJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'name'
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function collections_can_be_filtered_by_name()
    {
        User::factory()->create();

        Collection::factory()->count(10)->create(['name' => 'false']);
        Collection::factory()->count(2)->create(['name' => 'true']);

        $this->get('/api/collections?name=true');

        $this->assertEquals(200, $this->response->status());
        $this->seeJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'name'
                ]
            ]
        ]);
        $this->assertEquals(2, count(json_decode($this->response->getContent())->data));
    }

    /**
     * @test
     */
    public function collections_can_be_filtered_asc()
    {
        User::factory()->create();

        Collection::factory()->create(['name' => 'c']);
        Collection::factory()->create(['name' => 'b']);
        Collection::factory()->create(['name' => 'a']);

        $this->get('/api/collections?asc');

        $this->assertEquals(200, $this->response->status());
        $response = (json_decode($this->response->getContent())->data);

        foreach ($response as $row) {
            $order[] = $row->name;
        }

        $this->assertEquals(['a', 'b', 'c'], $order);
    }

    /**
     * @test
     */
    public function collections_can_be_filtered_desc()
    {
        // Image factory require at least 1 user
        User::factory()->create();

        Collection::factory()->create(['name' => 'a']);
        Collection::factory()->create(['name' => 'b']);
        Collection::factory()->create(['name' => 'c']);

        $this->get('/api/collections?desc');

        $this->assertEquals(200, $this->response->status());
        $response = (json_decode($this->response->getContent())->data);

        foreach ($response as $row) {
            $order[] = $row->name;
        }

        $this->assertEquals(['c', 'b', 'a'], $order);
    }

    // End Index

    // Show

    /**
     * @test
     */
    public function should_return_specified_collection()
    {
        User::factory()->create();
        $collection = Collection::factory()->create();

        $this->get("/api/collections/$collection->id");

        $this->assertEquals(200, $this->response->status());
        $this->seeJsonStructure([
            'data' => [
                'id',
                'user_id',
                'name'
            ]
        ]);
        $this->seeJson([
            'data' => [
                'id' => $collection->id,
                'user_id' => $collection->user_id,
                'name' => $collection->name
            ]
        ]);
    }

    /**
     * @test
     */
    public function should_return_error_if_collection_with_specified_id_does_not_exists_in_show_method()
    {
        User::factory()->create();
        $collection = Collection::factory()->create();

        $this->get("/api/collections/-1");

        $this->assertEquals(404, $this->response->status());
        $this->seeJsonStructure([
            'error'
        ]);
        $this->seeJson([
            'error' => 'Collection not found!'
        ]);
    }

    // End Show

    // Delete

    /**
     * @test
     */
    public function should_delete_collction()
    {
        User::factory()->create();
        $collection = Collection::factory()->create();

        $this->seeInDatabase('collections', ['id' => $collection->id]);

        $this->delete("/api/collections/$collection->id");

        $this->assertEquals(200, $this->response->status());
        $this->seeJsonStructure([
            'data' => [
                'id',
                'user_id',
                'name'
            ]
        ]);
        $this->seeJson([
            'data' => [
                'id' => $collection->id,
                'user_id' => $collection->user_id,
                'name' => $collection->name
            ]
        ]);

        $this->notSeeInDatabase('collections', ['id' => $collection->id]);
    }

    // Error if collection with specified id does not exist in delete method

    // End Delete

    // Store

    /**
     * @test
     */
    public function should_create_collction()
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->post('/api/collections', [
                'name' => $name = 'Collection name',
            ]);

        $this->assertEquals(201, $this->response->status());
        $this->seeJsonStructure([
            'data' => [
                'id',
                'user_id',
                'name'
            ]
        ]);
        $this->seeJson([
            'data' => [
                'id' => 1,
                'user_id' => $user->id,
                'name' => $name
            ]
        ]);
        $this->seeInDatabase('collections', ['name' => $name]);
    }

    /**
     * @test
     */
    public function name_is_required_in_store_collection()
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->post('/api/collections', [
                'name' => '',
            ]);

        $this->assertEquals(422, $this->response->status());
        $this->seeJsonStructure([
            'name'
        ]);
    }

    /**
     * @test
     */
    public function collection_store_only_if_user_is_authenticated()
    {
    }

    // End Store

    // Update

    /**
     * @test
     */
    public function should_update_collection()
    {
        $user = User::factory()->create();
        $collection = Collection::factory()->create();

        $this
            ->actingAs($user)
            ->patch("/api/collections/$collection->id", [
                'name' => $collection->name . 'updated',
            ]);

        $this->seeJsonStructure([
            'data' => [
                'id',
                'user_id',
                'name'
            ]
        ]);
        $this->seeJson([
            'data' => [
                'id' => 1,
                'user_id' => 1,
                'name' => $collection->name . 'updated'
            ]
        ]);
        $this->seeInDatabase('collections', ['name' => $collection->name . 'updated']);
    }

    /**
     * @test
     */
    public function collection_update_only_if_user_is_authenticated()
    {
    }

    /**
     * @test
     */
    public function collection_update_only_if_user_is_owner_of_collection()
    {
    }

    // End Update

    // Repository Mock

    // End Repository Mock

    // Cache Mock

    // End Cache Mock
}
