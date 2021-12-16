<?php
namespace App\Http\Controllers\API;
use Session;
use Validator;
use App\Product;
use App\Tag;
use Illuminate\Support\Str;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TagApiController extends Controller
{
    /**
     * Get Products
     * Params Tag {id, slug}
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function get_products_by_tag(Request $request)
    {
        $slug = $request->params;
        $products = Product::with('tags')->whereHas('tags', function($query){
            $clauseType = is_numeric(request()->params) ? 'whereTagId' : 'whereSlug' ; 
            $query->$clauseType(request()->params);
        })->where('status','enable')->orderBy('id','desc')->paginate(20);

        if (!empty($products)) {
            return response()->json([
                'success' => true,
                'results'    => $products,
                'message' =>'Products loaded.'
            ]);
        }

        return response()->json([
                'success' => 400,
                'message' =>'No product found.'
        ]); 
    }
}