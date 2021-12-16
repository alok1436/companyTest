<?php

namespace App\Http\Controllers\API;
use File;
use App\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class BannerApiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        
    }

    /**
    * Get all banners.
    * role @API
    * JSON
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function get(Request $request)
    { 
        $banners = Banner::orderBy('id','DESC')->get();
        return response()->json([
            'success' => true,
            'results'    => $banners,
            'message' =>'Banners loaded.'
        ]);
    }
}
