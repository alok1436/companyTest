<?php
namespace App\Http\Controllers\API;
use Illuminate\Support\Collection;
use Session;
use Validator;
use App\Variant;
use App\Product;
use App\Category;
use App\Brand;
use App\Services\CategoryService;
use Illuminate\Support\Str;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CategoryApiController extends Controller
{
    /**
     * Get Products
     * Params Category {id, slug}
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */

    protected $service;

    public function __construct(CategoryService $service){
        $this->service = $service;
    }

    public function get_products_by_category(Request $request)
    {
        $this->clauseType = is_numeric(request()->params3) ? 'whereCategoryId' : 'whereSlug' ; 

        if(request()->params1 && request()->params2 && request()->params3){

            $products = Product::with('brands','tags')

            ->whereHas('categories', function($query){
            $query->{$this->clauseType}(request()->params1);
            })

            ->whereHas('categories', function($query){
            $query->{$this->clauseType}(request()->params2);
            })

            ->whereHas('categories', function($query){
            $query->{$this->clauseType}(request()->params3);
            })

            ->where('status','enable')->orderBy('id','desc')->paginate(20);
 
            $category = Category::{$this->clauseType}($request->params3)->first();
            
            if (!empty($products)) {
                return response()->json([
                    'success' => true,
                     'results' => $products,
                    'filters' => [
                        'categories'=> $category ? $category->nestedChildren : null,
                        'varients' => Variant::pluck('name','id'),
                        'brands'=> ($category == null) ? "" : $category->brands,
                        'tags'=> ($category == null) ? "" : $category->tags,
                        'price'=>array(
                            'minimum'=>'',
                            'maxmium'=>'',
                        ),
                    ],
                    'message' =>'Products loaded.'
                ]);
            }

        }else if(request()->params1 && request()->params2){

                $products = Product::with('brands','tags')

                ->whereHas('categories', function($query){
                $query->{$this->clauseType}(request()->params1);
                })

                ->whereHas('categories', function($query){
                $query->{$this->clauseType}(request()->params2);
                })

                ->where('status','enable')->orderBy('id','desc')->paginate(20);
 
                $category = Category::{$this->clauseType}($request->params2)->first();
                
                if (!empty($products)) {
                    return response()->json([
                        'success' => true,
                        'results' => $products,
                        'filters' => [
                            'categories'=> $category ? $category->nestedChildren()->where('status','Enable')->get() : null,
                            'varients' => Variant::pluck('name','id'),
                            'brands'=> ($category == null) ? "" : $category->brands,
                            'tags'=> ($category == null) ? "" : $category->tags,
                            'price'=>array(
                                'minimum'=>'',
                                'maxmium'=>'',
                            ),
                        ],
                        'message' =>'Products loaded.'
                    ]);
                }

                }else{

                $products = Product::with('brands','tags')->whereHas('categories', function($query){
                    
                $query->{$this->clauseType}(request()->params1);
                })->where('status','enable')->orderBy('id','desc')->paginate(20);

                $clauseType = is_numeric(request()->params1) ? 'whereId' : 'whereSlug' ;
                $category = Category::{$this->clauseType}($request->params1)->first();
                
                if (!empty($products)) {
                    return response()->json([
                        'success' => true,
                        'results' => $products,
                        'filters' => [
                            'categories'=> $category ? $category->nestedChildren()->where('status','Enable')->get() : null,
                            'varients' => Variant::orderBy('name','ASC')->get(),
                            'brands'=> ($category == null) ? "" : $category->brands,
                            'tags'=> ($category == null) ? "" : $category->tags,
                            'price'=>array(
                                'minimum'=>'',
                                'maxmium'=>'',
                            ),
                        ],
                        'message' =>'Products loaded.'
                    ]);
                }
                
                return response()->json([
                        'success' => 400,
                        'sub_categories' => ($category == null) ? "" : $this->getChildren($category->id),
                        'message' =>'No product found.'
                ]); 
            }

            return response()->json([
                    'success' => 400,
                    'message' =>'Invalid parameter.'
            ]); 
    }

    public function getChildren($parent_id=0){ 
        $categories= Category::where(['parent_id'=>$parent_id,'status'=>'Enable'])->orderBy('id', 'asc')->get(['id','name','slug','image'])->toArray();
        $children = array();
        $i = 0;   
        foreach ($categories as $key => $category) {
            $children[$i] = array();
            $children[$i] = $category;
            $children[$i]['children'] =$this->getChildren($category['id']);
            $i++;
        } 
        return $children;
    }

    public function get_all_categories(Request $request){
      
        $categories= Category::with('nestedChildren', 'parent')->where(['parent_id'=>0,'status'=>'Enable'])->orderBy('name','asc')->get();
        return response()->json([
            'success' => 200,
            'results' => $categories,
            'message' =>'Categories loaded'
        ]);
    }

    public function get_parent_categories(Request $request){
      
        $categories= Category::with('children', 'parent')->where(['parent_id'=>0,'status'=>'Enable'])->get();
        return response()->json([
            'success' => 200,
            'results' => $categories,
            'message' =>'Get parent categories.'
        ]);
    }

    public function get_parent_with_child_categories(Request $request){
        if($request->parent_id){
            return response()->json([
                'success' => true,
                'results' => $this->getChildren($request->parent_id),
                'message' =>'Get child categories.'
            ]);
        }
    }
    
}