<?php

namespace App\Http\Controllers\API;
use File;
use App\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OfferApiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function get(Request $request)
    { 
        $collection = Offer::select('*');

        if($request->position !=''){
            $collection->where('position',$request->position);
        }
        
        $offers = $collection->orderBy('id','DESC')->get();
        return response()->json([
            'success' => true,
            'results'    => $offers,
            'message' =>'Offers get successfull.'
        ]);
    }
}
