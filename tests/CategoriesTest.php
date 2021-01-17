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
    public function should_return_error_if_category_does_not_exist_in_show_method()
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

    /**
     * @test
     */
    public function should_delete_category()
    {
        $category = Category::factory()->create();

        $this->delete("/api/categories/$category->id");

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
        $this->notSeeInDatabase('categories', ['name' => $category->name]);
    }

    /**
     * @test
     */
    public function should_create_category()
    {
        $this->post('/api/categories', [
            'name' => 'Category'
        ]);

        $this->assertEquals(201, $this->response->status());
        $this->seeJson([
            'data' => [
                'id' => 1,
                'parent_id' => null,
                'name' => 'Category'
            ]
        ]);
        $this->seeInDatabase('categories', ['name' => 'Category']);
    }

    /**
     * @test
     */
    public function should_create_category_with_parent()
    {
        $category = Category::factory()->create();

        $this->post('/api/categories', [
            'name' => 'Category',
            'parent_id' => $category->id
        ]);

        $this->assertEquals(201, $this->response->status());
        $this->seeJson([
            'data' => [
                'id' => 2,
                'parent_id' => $category->id,
                'name' => 'Category'
            ]
        ]);
        $this->seeInDatabase('categories', ['name' => 'Category']);
    }

    /**
     * @test
     */
    public function name_is_required_in_store_category()
    {
        $this->post('/api/categories', [
            'name' => ''
        ]);

        $this->assertEquals(422, $this->response->status());
        $this->seeJsonStructure([
            'name'
        ]);
        $this->notSeeInDatabase('categories', ['name' => '']);
    }

    /**
     * @test
     */
    public function category_myst_be_unique()
    {
        Category::factory()->create(['name' => 'unique']);

        $this->post('/api/categories', [
            'name' => 'unique'
        ]);

        $this->assertEquals(422, $this->response->status());
        $this->seeJsonStructure([
            'name'
        ]);
    }

    /**
     * @test
     */
    public function should_update_category()
    {
        $category = Category::factory()->create();

        $this->patch("/api/categories/$category->id", [
            'name' => "$category->name-updated"
        ]);

        $this->assertEquals(200, $this->response->status());
        $this->seeJson([
            'id' => $category->id,
            'name' => "$category->name-updated",
            'parent_id' => $category->parent_id
        ]);
    }

    /**
     * @test
     */
    public function return_error_if_category_with_specified_parent_id_does_not_exists()
    {
        $category = Category::factory()->create();

        $this->patch("/api/categories/$category->id", [
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
    public function return_validation_error_if_category_model_does_not_exists_in_update_method()
    {
        $this->patch('/api/categories/-1', [
            'parent_id' => -1
        ]);

        $this->assertEquals(422, $this->response->status());
        $this->seeJsonStructure([
            'parent_id'
        ]);
    }
}
