<?php
namespace App\Http\Controllers\API;
  
use Session;
use App\Role;
use App\User;
use App\Brand;
use App\Category;
use App\Product;
use App\MenuItems;
use Illuminate\Support\Str;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;

class MenuApiController extends Controller
{
    /**
     * Get Menus
     * @Role: API
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function index(Request $request)
    {
        return response()->json([
            'success' => true,
            'results' => $this->getMenus(0),
            'message' =>'Menu items get by menu.'
        ]);       
    }
 

    public function getMenus($parent_id=0){ 
        $menus= MenuItems::where(['menu'=>2,'parent'=>$parent_id])->get();
        $children = array();
        $i = 0;   
        foreach ($menus as $key => $menu) {
            $children[$i] = array();
            $children[$i] = $menu;
            $children[$i]['children'] =$this->getMenus($menu->id);
            $i++;
        } 
        return $children;
    }
}