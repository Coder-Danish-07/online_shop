<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use Image;


class CategoryController extends Controller
{
    public function index(Request $request){
        $categories = Category::latest();
        if(!empty($request->get('keyword'))){
            $categories = $categories->where('name','like','%'.$request->get('keyword').'%');
        }
        $categories = $categories->paginate(10);
        $data['categories'] = $categories;
        return view('admin.category.index',$data);
    }

    public function create(){
        return view('admin.category.create');
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:categories',
        ]);

        if($validator->passes()){
            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();

            //Save Image Here
            if(!empty($request->image_id)){
                $Tempimage = TempImage::find($request->image_id);
                $extArray = explode('.',$Tempimage->name);
                $ext = last($extArray);

                $newImageName = $category->id.'.'.$ext;
                $sPath = public_path().'/temp/'.$Tempimage->name;
                $dPath = public_path().'/uploads/category/'.$newImageName;
                File::copy($sPath,$dPath);
                

                // image in thumbnail
                $dtPath = public_path().'/uploads/category/thumb/'.$newImageName;
                $img = Image::make($sPath);
                // $img->resize(450, 600);
                $img->fit(450, 600, function ($constraint) {
                    $constraint->upsize();
                });
                $img->save($dtPath);

                $category->image = $newImageName;
                $category->save();


            }

            $request->session()->flash('success','Category Added Succesfully');

            return response()->json([
                'status' => true,
                'message' => "Category Added Succesfully",
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function edit($categoryID, Request $request){
        $category = Category::find($categoryID);

        if(empty($category)){
            return redirect()->route('category.index');
        }
        
        // dd($category);
        return view('admin.category.edit',compact('category'));
    }

    public function update($categoryID, Request $request){
        
        $category = Category::find($categoryID);
        if(empty($category)){
            $request->session()->flash('error','Category not found');
            return response()->json([
                'status' => false,
                'message' => 'Category not found',
                'NotFound' => true,
            ]);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$category->id.',id',
        ]);

        if($validator->passes()){
           
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();
            
            $oldImage = $category->image;
            //Save Image Here
            if(!empty($request->image_id)){
                $Tempimage = TempImage::find($request->image_id);
                $extArray = explode('.',$Tempimage->name);
                $ext = last($extArray);

                $newImageName = $category->id.'-'.time().'.'.$ext;
                $sPath = public_path().'/temp/'.$Tempimage->name;
                $dPath = public_path().'/uploads/category/'.$newImageName;
                File::copy($sPath,$dPath);
                

                // image in thumbnail
                $dtPath = public_path().'/uploads/category/thumb/'.$newImageName;
                $img = Image::make($sPath);
                // $img->resize(450, 600);
                // add callback functionality to retain maximal original image size
                $img->fit(450, 600, function ($constraint) {
                $constraint->upsize();
                });
                $img->save($dtPath);

                $category->image = $newImageName;
                $category->save();
                // delete image  old 
                File::delete(public_path().'/uploads/category/thumb/'.$oldImage);
                File::delete(public_path().'/uploads/category/'.$oldImage);


            }

            $request->session()->flash('success','Category Updated Succesfully');

            return response()->json([
                'status' => true,
                'message' => "Category Updated Succesfully",
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

    }

    public function destroy($categoryID, Request $request){

        $category = Category::find($categoryID);
        if(empty($category)){
            // return readirect()->route('category.index');
            $request->session()->flash('error','Category Not Found');
            return response()->json([
                'status' => true,
                'message' => 'Category Not Found',
            ]);
        }

        File::delete(public_path().'/uploads/category/thumb/'.$category->image);
        File::delete(public_path().'/uploads/category/'.$category->image);
        
        $request->session()->flash('success','Category Deleted Successfully');

        $category->delete();
        return response()->json([
            'status' => true,
            'message' => 'Category Deleted Successfully',
        ]);
    }
}
