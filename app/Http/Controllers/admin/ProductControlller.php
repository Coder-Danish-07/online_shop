<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\TempImage;
use Image;

class ProductControlller extends Controller
{
    public function create(){
        $categories = Category::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        $brands = Brand::orderBy('name','ASC')->get();
        $data['brands'] = $brands;
        return view('admin.product.create',$data);
    }

    public function store(Request $request){

        // dd($request->image_array);
        // die();
        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
        ];
        if(!empty($request->track_qty) && $request->track_qty='Yes'){
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(),$rules);

        if($validator->passes()){
            $product = new Product();
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->save();

            //Save Gallery Pics
            if(!empty($request->image_array)){
                foreach($request->image_array as $temp_image_id){

                    //Extention image
                    $tempImageInfo = TempImage::find($temp_image_id);
                    $extArray = explode('.',$tempImageInfo->name);
                    $ext = last($extArray);

                    //Image Save
                    $productImage = new ProductImage;
                    $productImage->product_id = $product->id;
                    $productImage->image = "NULL";
                    $productImage->save();

                    //create image name
                    $imageName  = $product->id.'-'.$productImage->id.'-'.time().'.'.$ext;
                    $productImage->image = $imageName;
                    $productImage->save();

                    //Generate Product Thumbnail

                    //Large Thumbnail
                    $sourcePath = public_path().'/temp/'.$tempImageInfo->name;
                    $destPath = public_path().'/uploads/product/large/'.$tempImageInfo->name;
                    $image = Image::make($sourcePath);
                    $image->resize(1400, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $image->save($destPath);

                    //Small Thumbnail
                    $destPath = public_path().'/uploads/product/small/'.$tempImageInfo->name;
                    $image = Image::make($sourcePath);
                    $image->fit(300,300);
                    $image->save($destPath);

                }
            }

            $request->session()->flash('success','Product Added Successfully');
            return response()->json([
                'status' =>true,
                'message' => 'Product Added Successfully',
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'errors'  => $validator->errors(),
            ]);
        }
    }
}
