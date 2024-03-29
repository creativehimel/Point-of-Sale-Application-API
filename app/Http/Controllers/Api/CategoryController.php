<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index():JsonResponse
    {
        $userId = auth()->user()->id;
        $categories = Category::select('id', 'name', 'description', 'image', 'status')->where('user_id', $userId)->get();
        return response()->json([
            'status' => 200,
            'data' => $categories
        ], 200);
    }

    public function store(Request $request):JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:categories,name',
        ]);
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => auth()->user()->id,
            'status' => $request->status,
        ];
        if($request->hasFile('image')){
            $image = $request->file('image');
            $image_name = 'categories-'.time() . '.' . $image->getClientOriginalExtension();
            $destination = public_path('uploads/images/categories');
            $image->move($destination, $image_name);
            $data['image'] = $image_name;
        }
        $categories = Category::create($data);
        if(!$categories){
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong'
            ]);
        }else{
            return response()->json([
                'status' => 201,
                'message' => 'Category created successfully',
                'data' => $categories
            ], 201);
        }
    }

    public function edit($id):JsonResponse
    {
        $categories = Category::find($id);
        if(!$categories){
            return response()->json([
                'status' => 404,
                'message' => 'Category not found'
            ]);
        }
        return response()->json([
            'status' => 200,
            'data' => $categories
        ], 200);
    }

    public function update(Request $request, $id):JsonResponse
    {
        $category = Category::find($id);
        $request->validate([
            'name' => 'required|string|max:50|unique:categories,name,'.$category->id,
        ]);
        if(!$category){
            return response()->json([
                'status' => 404,
                'message' => 'Category not found'
            ]);
        }
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => auth()->user()->id,
            'status' => $request->status,
        ];
        if($request->hasFile('image')){
            $image = $request->file('image');
            $image_name = 'categories-'.time() . '.' . $image->getClientOriginalExtension();
            $destination = public_path('uploads/images/categories');
            $image->move($destination, $image_name);
            $data['image'] = $image_name;
        }
        $category->update($data);
        if(!$category){
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong'
            ]);
        }else{
            return response()->json([
                'status' => 201,
                'message' => 'Category updated successfully',
                'data' => $category
            ], 201);
        }
    }

    public function destroy($id):JsonResponse
    {
        $category = Category::find($id);
        if(!$category){
            return response()->json([
                'status' => 404,
                'message' => 'Category not found'
            ]);
        }
        $category->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Category deleted successfully'
        ], 200);
    }
}
