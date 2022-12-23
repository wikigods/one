<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** * @test */
    public function category_get_route_key_name()
    {
        $category = Category::factory()->create();

        $this->assertEquals('slug', $category->getRouteKeyName());
    }

    /** @test */
    public function a_category_has_many_posts()
    {
        $category = Category::factory()->create();

        Post::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Post::class, $category->posts->first());
        $this->assertInstanceOf(HasMany::class, $category->posts());
    }
}
