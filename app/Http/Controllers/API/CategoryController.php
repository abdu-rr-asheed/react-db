<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{

    public function index()
    {
        $category = Category::all();
        return response()->json([
                'status'=>200,
                'category'=>$category,
            ]);
    }

    public function allcategory()
    {
        $category = Category::where('status', '0')->get();
        return response()->json([
                'status'=>200,
                'category'=>$category,
            ]);
    }

    public function edit($id)
    {
        $category = Category::find($id);
        if ($category) {
            return response()->json([
                'status'=>200,
                'category'=>$category,
            ]);
        } else {
            return response()->json([
                'status'=>404,
                'message'=>'No Category ID Found',
            ]);
        }
        
        
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'meta_title'=>"required",
            'slug'=>"required|max:191",
            'name'=>"required|max:191",
            'image'=>"required|image|mimes:jpeg,png,jpg|dimensions:ratio=3/4",
        ]);

        if ($validator->fails()) {
           return response()->json([
                'status'=>400,
                'errors'=>$validator->messages(),
            ]);
        } else {
            
            $category = new Category;
            $category->meta_title = $request->input('meta_title');
            $category->meta_keywords = $request->input('meta_keywords');
            $category->meta_description = $request->input('meta_description');
            $category->slug = $request->input('slug');
            $category->name = $request->input('name');
            $category->description = $request->input('description');

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extention = $file->getClientOriginalExtension();
                $filename = time() .'.'.$extention;
                $file->move('uploads/category/',$filename);
                $category->image = 'uploads/category/'.$filename;
            }
            
            $category->status = $request->input('status') == true ? '1' : '0';
            $category->save();

            return response()->json([
                'status'=>200,
                'message'=>"Category Added Successfully",
            ]);
        }
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(),[
            'meta_title'=>"required",
            'slug'=>"required|max:191",
            'name'=>"required|max:191",
        ]);

        if ($validator->fails()) {
           return response()->json([
                'status'=>422,
                'errors'=>$validator->messages(),
            ]);
        } else {
            
            $category = Category::find($id);
            if ($category) {
                $category->meta_title = $request->input('meta_title');
                $category->meta_keywords = $request->input('meta_keywords');
                $category->meta_description = $request->input('meta_description');
                $category->slug = $request->input('slug');
                $category->name = $request->input('name');
                $category->description = $request->input('description');

                if ($request->hasFile('image')) {
                    $path = $category->image;
                    if (File::exists($path)) {
                        File::delete($path);
                    }
                    $file = $request->file('image');
                    $extention = $file->getClientOriginalExtension();
                    $filename = time() .'.'.$extention;
                    $file->move('uploads/category/',$filename);
                    $category->image = 'uploads/category/'.$filename;
                }

                $category->status = $request->input('status') == true ? '1' : '0';
                $category->save();

                return response()->json([
                    'status'=>200,
                    'message'=>"Category Update Successfully",
                ]);
            } else {
                return response()->json([
                    'status'=>404,
                    'message'=>"No Category ID found",
                ]);
            }
            
            
        }
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if ($category) {
            $destination = $category->profile_photo;
            if (File::exists($destination)) {
                File::delete($destination);
            }
            $category->delete();
            return response()->json([
                'status'=>200,
                'message'=>"Category deleted Successfully",
            ]);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>"No Category ID found",
            ]);
        }
    }
}
