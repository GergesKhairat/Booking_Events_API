<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Services\CategoryService;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public $categoryService;
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    //All categories

    public function index()
    {
        $categories = $this->categoryService->index();
        return response()->json([
            "success" => true,
            "Categories" => CategoryResource::collection($categories)
        ], 200);
    }
    //select one
    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                "success" => false,
                "message" => "Category not found"
            ], 404);
        }
        return response()->json([
            "success" => true,
            "Category" => new CategoryResource($category)
        ]);
    }

    public function create(CreateCategoryRequest $request)
    {
        $request->validated();

        $category = $this->categoryService->create($request);

        return response()->json([
            'success' => true,
            'message' => "created successfully",
            'category' => new CategoryResource($category)
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            "title" => "required|String|min:3"
        ]);

        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                "success" => false,
                "message" => "Category not found"
            ], 404);
        }

        $category->update([
            "title" => $request->title
        ]);

        return response()->json([
            "success" => true,
            "message" => "category updated successfully",
            "category" => new CategoryResource($category)
        ], 200);
    }

    public function delete($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                "success" => false,
                "message" => "Category not found"
            ], 404);
        }
        $category->delete();
        return response()->json([
            "success" => true,
            "message" => "Category Deleted successfully"
        ], 200);
    }
}
