<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardPostRequest;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $posts = Post::paginate();

        return view('admin.posts.index', [
            'posts' => $posts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        $post = new Post;
        $categories = Category::all();

        return view('admin.posts.create', [
            'post' => $post,
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param GuardPostRequest $request
     * @return RedirectResponse
     */
    public function store(GuardPostRequest $request): RedirectResponse
    {
        $post = new Post($request->validated());
        $post->cover = $request->file('cover')->store('/');
        $post->user_id = auth()->user()->id;
        $post->save();

        return to_route('wg-admin.posts.index')->with('success', __('Created'));
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return View
     */
    public function show(Post $post): View
    {
        return view('admin.posts.show', [
            'post' => $post,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Post $post
     * @return View
     */
    public function edit(Post $post): View
    {
        $categories = Category::all();

        return view('admin.posts.create', [
            'post' => $post,
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param GuardPostRequest $request
     * @param Post $post
     * @return RedirectResponse
     */
    public function update(GuardPostRequest $request, Post $post): RedirectResponse
    {
        $post->update($request->validated());

        return to_route('wg-admin.posts.index')->with('success', __('Updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Post $post
     * @return RedirectResponse
     */
    public function destroy(Post $post): RedirectResponse
    {
        Storage::delete($post->cover);

        $post->delete();

        return to_route('wg-admin.posts.index')->with('success', __('Deleted'));
    }
}
