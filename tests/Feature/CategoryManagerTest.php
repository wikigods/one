<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryManagerTest extends TestCase
{
    use RefreshDatabase;

    protected mixed $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** * @test */
    public function guest_category_index()
    {
        $response = $this->get(route('wg-admin.category.index'));

        $response->assertRedirect(route('login'));
    }

    /** * @test */
    public function admin_category_index()
    {
        Category::factory(4)->create();

        $response = $this->actingAs($this->user)->get(route('wg-admin.category.index'));

        $categories = Category::paginate();

        $response->assertViewIs('admin.category.index');
        $response->assertViewHas('categories', $categories);
        $this->assertDatabaseCount('category', 4);
    }

    /** * @test */
    public function admin_category_store()
    {
        $response = $this->actingAs($this->user)->post(route('wg-admin.category.store'), [
            'title' => 'New Category',
            'slug' => 'new-category',
        ]);

        $category = Category::first();

        $response->assertRedirect(route('wg-admin.category.edit', $category));
        $response->assertSessionHas('status', __('Created'));

        $this->assertDatabaseHas('category', [
            'title' => 'New Category',
            'slug' => 'new-category',
        ]);
    }

    /** * @test */
    public function admin_category_edit()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->get(route('wg-admin.category.edit', $category));

        $category = Category::first();

        $response->assertViewIs('admin.category.edit');
        $response->assertViewHas('category', $category);
        $this->assertDatabaseCount('category', 1);
    }

    /** * @test */
    public function admin_category_update()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->put(route('wg-admin.category.update', $category), [
            'title' => 'Edit Category',
            'slug' => 'edit-category',
        ]);

        $response->assertRedirect(route('wg-admin.category.index'));
        $response->assertSessionHas('status', __('Updated'));

        $this->assertDatabaseHas('category', [
            'title' => 'Edit Category',
            'slug' => 'edit-category',
        ]);
    }

    /** * @test */
    public function admin_category_delete()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('wg-admin.category.destroy', $category));

        $response->assertRedirect(route('wg-admin.category.index'));
        $this->assertModelMissing($category);
    }

    /** * @test */
    public function except_category_create_and_show()
    {
        //method create
        $response = $this->actingAs($this->user)->call('GET', route('wg-admin.category.index').'/create');

        $this->assertEquals(405, $response->status());

        //method show
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->call('GET', route('wg-admin.category.index').'/'.$category->getRouteKey());

        $this->assertEquals(405, $response->status());

    }
}
