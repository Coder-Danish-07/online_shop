<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Brand;


class BrandController extends Controller
{
    public function index(Request $request){
        $brands = Brand::latest('id');
        if(!empty($request->get('keyword'))){
            $brands = $brands->where('name','like','%'.$request->get('keyword').'%');
        }
        $data['brands'] = $brands->paginate(10);
        return view('admin.brand.index',$data);
    }
    public function create(){
        return view('admin.brand.create');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:brands',
            'status' => 'required',
        ]);

        if($validator->passes()){
            $brands = new Brand();
            $brands->name = $request->name;
            $brands->slug = $request->slug;
            $brands->status = $request->status;
            $brands->save();

            $request->session()->flash('success','Brand Added Succesfully');

            return response()->json([
                'status' => true,
                'message' => 'Brand Added Succesfully',
            ]);


        }
        else{
            return response()->json([
                'status' =>false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function edit($id, Request $request){
        $brands = Brand::find($id);
        if(empty($brands)){
            $request->session()->flash('error','Brands Not Found');
            return redirect()->route('brands.index');
        }
        $data['brands'] = $brands;
        return view('admin.brand.edit',$data);
    }

    public function update($id,Request $request){
        $brands = Brand::find($id);
        if(empty($brands)){
            $request->session()->flash('error','Brand Record Not Found');
            return response()->json([
                'status' =>false,
                'NoFound' => true,
            ]);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$brands->id.',id',
            'status' => 'required',
        ]);

        if($validator->passes()){
            $brands->name = $request->name;
            $brands->slug = $request->slug;
            $brands->status = $request->status;
            $brands->save();

            $request->session()->flash('success','Brand update Succesfully');

            return response()->json([
                'status' => true,
                'message' => 'Brand update Succesfully',
            ]);
        }
        else{
            return response()->json([
                'status' =>false,
                'errors' => $validator->errors(),
            ]);
        }

    }

    public function destroy($id,Request $request){
        $brands = Brand::find($id);
        if(empty($brands)){
            $request->session()->flash('error','Brand Record Not Found');
            return response()->json([
                'status' => false,
                'NoFound' => true,
            ]);
        }
        $request->session()->flash('success','Brand Deleted Successfully');
        $brands->delete();
        return response()->json([
            'status' => true,
            'message' => 'Brand Deleted Successfully',
        ]);
    }
}
