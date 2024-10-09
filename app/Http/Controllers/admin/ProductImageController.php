<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductImage;
use Illuminate\Support\Facades\File;
// use Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductImageController extends Controller
{
    public function update(Request $request)
    {

        //Extention image
        // dd($request->all());
        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $sourcePath = $image->getPathName();


        //Image Save
        $productImage = new ProductImage;
        $productImage->product_id = $request->product_id;
        $productImage->image = "NULL";
        $productImage->save();

        //create image name
        $imageName  = $request->product_id . '-' . $productImage->id . '-' . time() . '.' . $ext;
        $productImage->image = $imageName;
        $productImage->save();

        //Generate Product Thumbnail

        //Large Thumbnail
        $destPath = public_path() . '/uploads/product/large/' . $imageName;
        // $image = Image::make($sourcePath);
        // $image->resize(1400, null, function ($constraint) {
        //     $constraint->aspectRatio();
        // });
        // $image->save($destPath);
        $manager = new ImageManager(new Driver());
        $image = $manager->read($sourcePath);
        $image->scaleDown(1400);
        $image->save($destPath);

        //Small Thumbnail
        $destPath = public_path() . '/uploads/product/small/' . $imageName;
        // $image = Image::make($sourcePath);
        // $image->fit(300, 300);
        // $image->save($destPath);
        $manager = new ImageManager(new Driver());
        $image = $manager->read($sourcePath);
        $image->cover(300,300);
        $image->save($destPath);

        return response()->json([
            'status' => true,
            'image_id'=> $productImage->id,
            'imagePath' => asset('uploads/product/small/'.$productImage->image),
            'message'=>'Image Saved Successfully',
        ]);
    }

    public function destory(Request $request){
        $productImage = ProductImage::find($request->id);

        if(empty($productImage)){
            return response()->json([
                'status' => false,
                'message' => 'Image Not Found',
            ]);
        }

        //Delete image for folder
        File::delete(public_path('uploads/product/large/'.$productImage->image));
        File::delete(public_path('uploads/product/small/'.$productImage->image));

        $productImage->delete();

        return response()->json([
            'status' => true,
            'message' => 'Image Deleted Successfully',
        ]);

    }
}
