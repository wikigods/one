<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $categories = Category::paginate();

        return view('admin.category.index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param GuardCategoryRequest $request
     * @return RedirectResponse
     */
    public function store(GuardCategoryRequest $request): RedirectResponse
    {
        $category = Category::create($request->validated());

        return to_route('wg-admin.category.edit', $category)->with('status', __('Created'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Category $category
     * @return View
     */
    public function edit(Category $category): View
    {
        return view('admin.category.edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param GuardCategoryRequest $request
     * @param Category $category
     * @return RedirectResponse
     */
    public function update(GuardCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return to_route('wg-admin.category.index')->with('status', __('Updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @return RedirectResponse
     */
    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return to_route('wg-admin.category.index');
    }
}
