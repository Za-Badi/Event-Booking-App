<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategory;
use App\Http\Requests\UpdateCategory;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CategoryController extends Controller
{
    public function index()
    {

        $categories = Category::get();
        return response()->json([
            'success' => true,
            'count' => $categories->count(),
            'data' =>  CategoryResource::collection($categories)
        ], 200);
    }
    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category)
        ], 200);
    }


    public function store(CreateCategory $request)
    {
        // validation
        $request->validated();

        //  create
        $category = Category::create([
            'title' => $request->title,
        ]);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not created'
            ], 400);
        }
        // response
        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
        ], 200);
    }
    public function update(UpdateCategory $request, $id)
    {
        // validation
        $request->validated();

        //  find
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }
        // update
        $category->update([
            'title' => $request->title,
        ]);


        // response
        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
        ], 200);
    }

      public function destroy($id)
    {
      
        //  find
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }
        // update
        $category->delete();
      
       
        // response
        return response()->json([
            'success' => true,
            'message' => 'Category Deleted Successfully',
        ], 200);
    }
}
