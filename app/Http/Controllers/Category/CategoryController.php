<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategory;
use App\Http\Requests\UpdateCategory;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategory;
use App\Http\Requests\UpdateCategory;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('title', 'asc')->paginate(10);

        return CategoryResource::collection($categories)->additional([
            'success' => true,
            'total' => $categories->total(),
            'pages_left' => $categories->lastPage() - $categories->currentPage(),
        ]);
    }

    public function show(Category $category)
    {
        return (new CategoryResource($category))->additional([
            'success' => true
        ]);
    }

    public function store(CreateCategory $request)
    {
        $this->authorize('manage', Category::class);

        $category = Category::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => new CategoryResource($category),
        ], 201);
    }

    public function update(UpdateCategory $request, Category $category)
    {
        $this->authorize('update', $category);

        $category->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => new CategoryResource($category->fresh()),
        ]);
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        $category->delete();
        return response()->json([
            'success' => true,
            'message' => 'Category Deleted Successfully',
        ]);
    }
}
