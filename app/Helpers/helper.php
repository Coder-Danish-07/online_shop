<?php
use App\Models\Category;
use App\Models\ProductImage;

function getCateories(){
    return Category::orderBy('name','ASC')
           ->with('sub_category')
           ->where('status',1)
           ->where('showHome','Yes')
        //    ->orderBy('id','DESC')
           ->get();
}

function ProductImage($product_id){
   return ProductImage::where('product_id',$product_id)->first();
}
?>