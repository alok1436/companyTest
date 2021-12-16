<?php

namespace App\Services;

use App\Category;
use App\Brand;
use Auth;

class CategoryService
{
    /**
     * @param $title
     * @param int $id
     * @return string
     * @throws \Exception
     */
    
    public function brand(){
          return Brand::get();
    } 
    // public function subCategory($category_id){
    //       return Category::where('parent_id',$category_id)->get();
    // }

}