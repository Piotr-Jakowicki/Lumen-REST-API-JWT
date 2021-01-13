<?php

use App\Models\Category;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CategoriesTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function list_categories()
    {
        Category::factory()->count(10)->make();

        $this->get('/api/categories');

        $this->assertEquals(200, $this->response->status());
        $this->seeJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'parent_id',
                    'name'
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function list_can_be_filtered_by_name()
    {
        Category::factory()->count(10)->create(['name' => 'false']);
        Category::factory()->count(2)->create(['name' => 'true']);

        $this->get('/api/categories?name=true');

        $this->assertEquals(200, $this->response->status());
        $this->seeJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'parent_id',
                    'name'
                ]
            ]
        ]);
        $this->assertEquals(2, count(json_decode($this->response->getContent())->data));
    }

    /**
     * @test
     */
    public function list_can_be_sorted_asc_by_name()
    {
        Category::factory()->create(['name' => 'c']);
        Category::factory()->create(['name' => 'b']);
        Category::factory()->create(['name' => 'a']);

        $this->get('/api/categories?asc');

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
    public function list_can_be_sorted_desc_by_name()
    {
        Category::factory()->create(['name' => 'a']);
        Category::factory()->create(['name' => 'b']);
        Category::factory()->create(['name' => 'c']);

        $this->get('/api/categories?desc');

        $this->assertEquals(200, $this->response->status());
        $response = (json_decode($this->response->getContent())->data);

        foreach ($response as $row) {
            $order[] = $row->name;
        }

        $this->assertEquals(['c', 'b', 'a'], $order);
    }

    /**
     * @test
     */
    public function should_return_specified_category()
    {
        $category = Category::factory()->create();

        $this->get("/api/categories/$category->id");

        $this->assertEquals(200, $this->response->status());
        $this->seeJsonStructure([
            'data' => [
                'id',
                'parent_id',
                'name'
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
    public function should_return_error_if_category_does_not_exist()
    {
        $category = Category::factory()->create();

        $this->get('/api/categories/-1');

        $this->assertEquals(404, $this->response->status());
        $this->seeJsonStructure([
            'error'
        ]);
        $this->seeJson([
            'error' => 'Category not found!'
        ]);
    }
}
