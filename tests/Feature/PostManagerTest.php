<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        Storage::fake('public');
        $cover = UploadedFile::fake()->image('cover.jpg');
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->post(route('wg-admin.posts.store'), [
            'cover' => $cover,
            'title' => 'New Post',
            'slug' => 'new-post',
            'excerpt' => 'New Excerpt',
            'content' => 'New Content',
            'published_at' => '12/25/2022',
            'user_id' => $this->user->id,
            'category_id' => $category->id
        ]);

        $post = Post::first();

        $response->assertRedirect(route('wg-admin.posts.index'));
        $response->assertSessionHas('success', __('Created'));

        $this->assertDatabaseHas('posts', [
            'cover' => $coverPath  = Storage::disk('public')->files()[0],
            'title' => 'New Post',
            'slug' => 'new-post',
            'excerpt' => 'New Excerpt',
            'content' => 'New Content',
            'published_at' => '2022-12-25 00:00:00',
            'user_id' => $this->user->id,
            'category_id' => $category->id
        ]);

        $this->assertInstanceOf(Carbon::class, $post->published_at);
        $this->assertEquals('12/25/2022', $post->published_at->format('m/d/Y'));
        Storage::disk('public')->assertExists($coverPath);
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
        $response->assertSessionHas('success', __('Updated'));

        $this->assertDatabaseHas('posts', [
            'title' => 'Edit Post',
            'slug' => 'edit-post',
            'category_id' => 1
        ]);
    }

    /** * @test */
    public function admin_posts_delete()
    {
        Storage::fake('public');
        $cover = UploadedFile::fake()->image('cover.jpg')->store('/');

        $post = Post::factory()->create([
            'cover' => $cover
        ]);

        $response = $this->actingAs($this->user)->delete(route('wg-admin.posts.destroy', $post));

        $response->assertRedirect(route('wg-admin.posts.index'));
        $response->assertSessionHas('success', __('Deleted'));
        Storage::disk('public')->assertMissing($cover);
        $this->assertModelMissing($post);

    }

    //validation
    /** * @test */
    public function admin_posts_cover_required()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->post(route('wg-admin.posts.store'), [
            'cover' => '',
            'title' => 'New Post',
            'slug' => 'new-post',
            'excerpt' => 'New Excerpt',
            'content' => 'New Content',
            'published_at' => '12/25/2022',
            'user_id' => $this->user->id,
            'category_id' => $category->id
        ]);

        $response->assertSessionHasErrors('cover');
    }

    /** * @test */
    public function admin_posts_slug_required()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->post(route('wg-admin.posts.store'), [
            'cover' => 'aa',
            'title' => 'New Post',
            'slug' => null,
            'excerpt' => 'New Excerpt',
            'content' => 'New Content',
            'published_at' => '12/25/2022',
            'user_id' => $this->user->id,
            'category_id' => $category->id
        ]);

        $response->assertSessionHasErrors('slug');
    }

    /** * @test */
    public function admin_posts_slug_unique()
    {
        $category = Category::factory()->create();
        $post = Post::factory()->create();
        $response = $this->actingAs($this->user)->post(route('wg-admin.posts.store'), [
            'cover' => 'aa',
            'title' => 'New Post',
            'slug' => $post->slug,
            'excerpt' => 'New Excerpt',
            'content' => 'New Content',
            'published_at' => '12/25/2022',
            'user_id' => $this->user->id,
            'category_id' => $category->id
        ]);

        $response->assertSessionHasErrors('slug');
    }
}
