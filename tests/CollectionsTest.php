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
}
