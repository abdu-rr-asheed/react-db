<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    public function index()
    {
        $product = Product::all();
        return response()->json([
            'status'=>200,
            'products'=>$product,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id'=>"required|max:191",
            'slug'=>"required|max:191",
            'name'=>"required|max:191",
            'meta_title'=>"required|max:191",
            'brand'=>"required|max:20",
            'meta_description'=>"required|max:191",
            'selling_price'=>"required|max:20",
            'original_price'=>"required|max:20",
            'quantity'=>"required|max:4",
            'image'=>"required|image|mimes:jpeg,png,jpg|dimensions:ratio=3/4",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'=>422,
                'errors'=>$validator->messages(),
            ]);
        }
        else{

            $count = Product::take(6);

            if ($count) {
                return response()->json([
                'status'=>200,
                'errors'=>$validator->messages(),
            ]);
            }
            else {

                $product = new Product;
                $product->category_id = $request->input('category_id');
                $product->slug = $request->input('slug');
                $product->name = $request->input('name');
                $product->description = $request->input('description');
    
                $product->meta_title = $request->input('meta_title');
                $product->meta_keyword = $request->input('meta_keyword');
                $product->meta_description = $request->input('meta_description');
    
                $product->brand = $request->input('brand');
                $product->selling_price = $request->input('selling_price');
                $product->original_price = $request->input('original_price');
                $product->quantity = $request->input('quantity');
    
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $extention = $file->getClientOriginalExtension();
                    $filename = time() .'.'.$extention;
                    $file->move('uploads/product/',$filename);
                    $product->image = 'uploads/product/'.$filename;
                }
    
                $product->featured = $request->input('featured');
                $product->popular = $request->input('popular');
                $product->status = $request->input('status');
                $product->save();
    
                return response()->json([
                    'status'=>200,
                    'message'=>'Product Added Successfully',
                ]);
            }

        }

    }

    public function edit($id)
    {
        $product = Product::find($id);
        if ($product) {
            return response()->json([
                'status'=>200,
                'product'=>$product,
            ]);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'No Product ID Found',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'category_id'=>"required|max:191",
            'slug'=>"required|max:191",
            'name'=>"required|max:191",
            'meta_title'=>"required|max:191",
            'brand'=>"required|max:20",
            'meta_description'=>"required|max:191",
            'selling_price'=>"required|max:20",
            'original_price'=>"required|max:20",
            'quantity'=>"required|max:4",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'=>422,
                'errors'=>$validator->messages(),
            ]);
        }
        else{

            $product = Product::find($id);

            if ($product) {
                
            $product->category_id = $request->input('category_id');
            $product->slug = $request->input('slug');
            $product->name = $request->input('name');
            $product->description = $request->input('description');

            $product->meta_title = $request->input('meta_title');
            $product->meta_keyword = $request->input('meta_keyword');
            $product->meta_description = $request->input('meta_description');

            $product->brand = $request->input('brand');
            $product->selling_price = $request->input('selling_price');
            $product->original_price = $request->input('original_price');
            $product->quantity = $request->input('quantity');

            if ($request->hasFile('image')) {
                $path = $product->image;
                if (File::exists($path)) {
                    File::delete($path);
                }
                $file = $request->file('image');
                $extention = $file->getClientOriginalExtension();
                $filename = time() .'.'.$extention;
                $file->move('uploads/product/',$filename);
                $product->image = 'uploads/product/'.$filename;
            }

            $product->featured = $request->input('featured') == true ? '1' : '0';
            $product->popular = $request->input('popular') == true ? '1' : '0';
            $product->status = $request->input('status') == true ? '1' : '0';
            $product->update();

            return response()->json([
                'status'=>200,
                'message'=>'Product Updated Successfully',
            ]);

            } else {
                return response()->json([
                'status'=>404,
                'message'=>'Product Not Not Found',
            ]);
                
            }
        }
    }
}
