<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TempImage;
// use Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TempImageController extends Controller
{
    public function create(Request $request){

        $image = $request->image;

        if(!empty($image)){
            $ext = $image->getClientOriginalExtension();
            //old
            // $newName = time().'.'.$ext;
            // $tempImage = new TempImage();
            // $tempImage->name = $newName;
            // $tempImage->save();

            //new
            $tempImage = new TempImage();
            $tempImage->name = 'TEST';
            $tempImage->save();

            $newName = $tempImage->id.'-'.time().'.'.$ext;
            $tempImage->name = $newName;
            $tempImage->save();

            $image->move(public_path().'/temp',$newName);

            //Generated Thumbnail
            $sourcePath = public_path().'/temp/'.$newName;
            $destPath = public_path().'/temp/thumb/'.$newName;
            // $image = Image::make($sourcePath);
            // $image->fit(300,275);
            // $image->save($destPath);
            $manager = new ImageManager(new Driver());
            $image = $manager->read($sourcePath);
            $image->cover(300, 275);
            $image->save($destPath);

            return response()->json([
                'status' => true,
                'image_id'=> $tempImage->id,
                'imagePath' => asset('/temp/thumb/'.$newName),
                'message' => "Image Uploaded Successfully"
            ]);
        }

    }
}
