<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostManagerTest extends TestCase
{
    use RefreshDatabase;

    protected mixed $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** * @test */
    public function guest_posts_index()
    {
        $response = $this->get(route('wg-admin.posts.index'));

        $response->assertRedirect(route('login'));
    }

    /** * @test */
    public function admin_posts_index()
    {

        Post::factory(4)->create();

        $response = $this->actingAs($this->user)->get(route('wg-admin.posts.index'));

        $posts = Post::paginate();

        $response->assertViewIs('admin.posts.index');
        $response->assertViewHas('posts', $posts);
        $this->assertDatabaseCount('posts', 4);
    }

    /** * @test */
    public function admin_posts_create()
    {
        $response = $this->actingAs($this->user)->get(route('wg-admin.posts.create'));

        $post = new Post;

        $categories = Category::all();

        $response->assertViewIs('admin.posts.create');
        $response->assertViewHas('post', $post);
        $response->assertViewHas('categories', $categories);
    }

    /** * @test */
    public function admin_posts_store()
    {
        $response = $this->actingAs($this->user)->post(route('wg-admin.posts.store'), [
            'title' => 'New Post',
            'slug' => 'new-post',
            'category_id' => 1
        ]);

        $post = Post::first();

        $response->assertRedirect(route('wg-admin.posts.edit', $post));
        $response->assertSessionHas('status', __('Created'));

        $this->assertDatabaseHas('posts', [
            'title' => 'New Post',
            'slug' => 'new-post',
            'category_id' => 1
        ]);
    }

    /** * @test */
    public function admin_posts_show()
    {
        $post = Post::factory()->create();

        $response = $this->actingAs($this->user)->get(route('wg-admin.posts.show', $post));

        $post = Post::first();

        $response->assertViewIs('admin.posts.show');
        $response->assertViewHas('post', $post);
    }

    /** * @test */
    public function admin_posts_edit()
    {
        $post = Post::factory()->create();

        $response = $this->actingAs($this->user)->get(route('wg-admin.posts.edit', $post));

        $post = Post::first();

        $categories = Category::all();

        $response->assertViewIs('admin.posts.create');
        $response->assertViewHas('post', $post);
        $response->assertViewHas('categories', $categories);
    }

    /** * @test */
    public function admin_posts_update()
    {
        $post = Post::factory()->create();

        $response = $this->actingAs($this->user)->put(route('wg-admin.posts.update', $post), [
            'title' => 'Edit Post',
            'slug' => 'edit-post',
            'category_id' => 1
        ]);

        $response->assertRedirect(route('wg-admin.posts.index'));
        $response->assertSessionHas('status', __('Updated'));

        $this->assertDatabaseHas('posts', [
            'title' => 'Edit Post',
            'slug' => 'edit-post',
            'category_id' => 1
        ]);
    }

    /** * @test */
    public function admin_posts_delete()
    {
        $post = Post::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('wg-admin.posts.destroy', $post));

        $response->assertRedirect(route('wg-admin.posts.index'));
        $this->assertModelMissing($post);
    }
}
