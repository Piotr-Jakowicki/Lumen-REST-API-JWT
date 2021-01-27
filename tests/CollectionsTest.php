<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use App\Models\Collection;
use App\Models\User;

class CollectionsTest extends TestCase
{
    use DatabaseMigrations;

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
}
