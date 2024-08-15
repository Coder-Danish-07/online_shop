<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use App\Models\SubCategory;

class SubCategoryController extends Controller
{
    public function index(Request $request){
        $subCategories = SubCategory::select('sub_categories.*','categories.name as categoryName')->latest('id')->leftjoin('categories','sub_categories.category_id','categories.id');
        if(!empty($request->get('keyword'))){
            $subCategories = $subCategories->where('sub_categories.name','like','%'.$request->get('keyword').'%');
            $subCategories = $subCategories->orWhere('categories.name','like','%'.$request->get('keyword').'%');
        }
        $subCategories = $subCategories->paginate(10);
        $data['subCategories'] = $subCategories;
        return view('admin.sub_category.index',$data);
        
    }
    public function create(){
        $categories = Category::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        return view('admin.sub_category.create',$data);
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:sub_categories',
            'category' => 'required',
            'status' => 'required',
        ]);

        if($validator->passes()){
            $sub_category = new SubCategory();
            $sub_category->name = $request->name;
            $sub_category->slug = $request->slug;
            $sub_category->status = $request->status;
            $sub_category->showHome = $request->showHome;
            $sub_category->category_id =$request->category;
            $sub_category->save();

            $request->session()->flash('success','Sub Category Added Succesfully');

            return response()->json([
                'status' =>true,
                'message' => 'Sub Category Added Succesfully',
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit($id, Request $request){
        $subCategory = SubCategory::find($id);
        if(empty($subCategory)){
            $request->session()->flash('error','Sub Category Not Found');
            return redirect()->route('sub-category.index');
        }
        $categories = Category::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        $data['subCategory'] = $subCategory;
        return view('admin.sub_category.edit',$data);
    }

    public function update($id, Request $request){

        $subCategory = SubCategory::find($id);
        if(empty($subCategory)){
            $request->session()->flash('error','Sub Category Record Not Found');
            return response()->json([
                'status' => false,
                'Nofound' => true,
            ]);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            // 'slug' => 'required|unique:sub_categories',
            'slug' => 'required|unique:sub_categories,slug,'.$subCategory->id.',id',
            'category' => 'required',
            'status' => 'required',
        ]);

        if($validator->passes()){

            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->showHome = $request->showHome;
            $subCategory->category_id =$request->category;
            $subCategory->save();

            $request->session()->flash('success','Sub Category Updated Succesfully');

            return response()->json([
                'status' =>true,
                'message' => 'Sub Category Updated Succesfully',
                'message' => 'Sub Category Updated Succesfully',
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy($id, Request $request){
        $subCategory = SubCategory::find($id);
        if(empty($subCategory)){
            $request->session()->flash('error','Sub Category Record Not Found');
            return response()->json([
                'status' => false,
                'Nofound' => true,
            ]) ;
        }
        
        $request->session()->flash('success','Sub Category Deleted Successfully');

        $subCategory->delete();
        return response()->json([
            'status' => true,
            'message' => 'Sub Category Deleted Successfully',
        ]);
    }
}
