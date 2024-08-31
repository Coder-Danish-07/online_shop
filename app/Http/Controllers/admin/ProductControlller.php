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
use App\Models\SubCategory;
use Illuminate\Support\Facades\File;

use Image;

class ProductControlller extends Controller
{
    public function index(Request $request){

        $products = Product::latest('id')->with('product_images');
        if($request->get('keyword') != ""){
            $products = $products->where('title','like','%'.$request->keyword.'%');
        }
        $products = $products->paginate(10);
        // dd($products);
        $data['products'] = $products; 
        return view('admin.product.index',$data);
    }
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
            $product->short_description = $request->short_description;
            $product->shipping_returns = $request->shipping_returns;
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
                    $destPath = public_path().'/uploads/product/large/'.$imageName;
                    $image = Image::make($sourcePath);
                    $image->resize(1400, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $image->save($destPath);

                    //Small Thumbnail
                    $destPath = public_path().'/uploads/product/small/'.$imageName;
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

    public function edit($id,Request $request){

        $product = Product::find($id);
        if(empty($product)){
            return redirect()->route('products.index')->with('error','Product Not Found');
        }

        //Fetch Product Image
        $productImage = ProductImage::where('product_id',$product->id)->get();

        $subCategories = SubCategory::where('category_id',$product->category_id)->get();
    //    dd($subCategories);
       
        $categories = Category::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        $brands = Brand::orderBy('name','ASC')->get();
        $data['brands'] = $brands;
        $data['products'] = $product;
        $data['subCategories'] = $subCategories;
        $data['productImage'] = $productImage;

        return view('admin.product.edit',$data);
    }

    public function update($id, Request $request){

        $product = Product::find($id);
        
        if(empty($product)){
            
            return redirect()->route('products.index')->with('error','Product not found');
        }
        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products,slug,'.$product->id.',id',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products,sku,'.$product->id.',id',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
        ];
        if(!empty($request->track_qty) && $request->track_qty='Yes'){
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(),$rules);

        if($validator->passes()){

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
            $product->short_description = $request->short_description;
            $product->shipping_returns = $request->shipping_returns;
            $product->save();
            
            $request->session()->flash('success','Product Updated Successfully');
            return response()->json([
                'status' =>true,
                'message' => 'Product Updated Successfully',
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'errors'  => $validator->errors(),
            ]);
        }
    }

    public function destroy($id,Request $request){
        $product = Product::find($id);
        
        if(empty($product)){

            $request->session()->flash('error','Product Not found');
            return response()->json([
                'status' => false,
                'message' => 'Product Not Found',
            ]);
        }

        $productImages = ProductImage::where('product_id',$id)->get();

        if(!empty($productImages)){
            foreach($productImages as $productImage){

                File::delete(public_path('uploads/product/large/'.$productImage->image));
                File::delete(public_path('uploads/product/small/'.$productImage->image));
            }
            ProductImage::where('product_id',$id)->delete();
        }

        $product->delete();

        $request->session()->flash('success','Product Deleted Succesfully');
        return response()->json([
            'status' => true,
            'message' => 'Product Deleted Succesfully',
        ]);
    }
}
