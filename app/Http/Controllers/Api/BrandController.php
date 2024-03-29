<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::select('id', 'name', 'description', 'image', 'status')->get();
        return response()->json([
            'status' => 200,
            'data' => $brands
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:brands,name',
        ]);
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => auth()->user()->id,
            'status' => $request->status,
        ];
        if($request->hasFile('image')){
            $image = $request->file('image');
            $image_name = 'brands-'.time() . '.' . $image->getClientOriginalExtension();
            $destination = public_path('uploads/images/brands');
            $image->move($destination, $image_name);
            $data['image'] = $image_name;
        }
        $brands = Brand::create($data);
        if(!$brands){
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong'
            ]);
        }else{
            return response()->json([
                'status' => 201,
                'message' => 'Brand created successfully',
                'data' => $brands
            ], 201);
        }
    }

    public function edit($id)
    {
        $brands = Brand::find($id);
        if(!$brands){
            return response()->json([
                'status' => 404,
                'message' => 'Brand not found'
            ], 404);
        }
        return response()->json([
            'status' => 200,
            'data' => $brands
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $brands = Brand::find($id);
        if(!$brands){
            return response()->json([
                'status' => 404,
                'message' => 'Brand not found'
            ], 404);
        }
        $request->validate([
            'name' => 'required|string|max:50|unique:brands,name,'.$brands->id,
        ]);
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => auth()->user()->id,
            'status' => $request->status,
        ];
        if($request->hasFile('image')){
            $image = $request->file('image');
            $image_name = 'brands-'.time() . '.' . $image->getClientOriginalExtension();
            $destination = public_path('uploads/images/brands');
            $image->move($destination, $image_name);
            $data['image'] = $image_name;
        }
        $brands->update($data);
        return response()->json([
            'status' => 200,
            'message' => 'Brand updated successfully',
        ]);
    }
    public function destroy($id)
    {
        $brands = Brand::find($id);
        if(!$brands){
            return response()->json([
                'status' => 404,
                'message' => 'Brand not found'
            ], 404);
        }
        $brands->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Brand deleted successfully'
        ], 200);
    }
}
