<?php

namespace App\Http\Controllers\api\v5;

use App\CPU\ImageManager;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use App\Model\Banner;
use App\Model\ProviderBanner;
use App\Model\Product;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;
use App\Model\ProviderWalletHistory;
use App\Model\Contact;
use App\Model\ServiceProviderPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use function App\CPU\translate;
use App\Model\FavouriteProviderList;
use App\Model\ProviderReviews;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Storage;

class BasicProviderController extends Controller
{
     use CommonTrait;
    public function info(Request $request)
    {
        return response()->json($request->user(), 200);
    }
    
   
    public function contact_us(Request $request)
    {
        $contact = new Contact();
        $contact->name = $request->full_name;
        $contact->email = $request->email;
        $contact->mobile_number = $request->mobile;
        $contact->business_name = $request->business_name ?? NULL;
        $contact->subject = 'Provider';
        $contact->message = $request->message;
        $contact->type = 1;
        $contact->save();
        return response()->json(['message' => translate('we are Contact you soon !'),
                'status' => true],200);
    }
    
     public function get_banners(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'banner_type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if ($request['banner_type'] == 'all') {
            $banners = ProviderBanner::where(['published' => 1])->get();
        } elseif ($request['banner_type'] == 'main_banner') {
            $banners = ProviderBanner::where(['published' => 1, 'banner_type' => 'Main Banner'])->get();
        } elseif ($request['banner_type'] == 'main_section_banner') {
            $banners = ProviderBanner::where(['published' => 1, 'banner_type' => 'Main Section Banner'])->get();
        }else {
            $banners = ProviderBanner::where(['published' => 1, 'banner_type' => 'Footer Banner'])->get();
        }
        
         $banners = $banners->map(function ($banners) {
                // Format URLs
                $imagePath = 'public/banner/' . $banners->photo;
                
                 if (Storage::exists($imagePath)) {
                        $banners->photo = asset('storage/banner/' . $banners->photo);
                    } else {
                      
                        $banners->photo = asset('storage/banner/default.jpeg');
                    }
          
                return $banners;
            });
        $pro_ids = [];
        $data = [];
       /* foreach ($banners as $banner) {
            if ($banner['resource_type'] == 'product' && !in_array($banner['resource_id'], $pro_ids)) {
                array_push($pro_ids,$banner['resource_id']);
                $product = Product::find($banner['resource_id']);
                $banner['product'] = Helpers::product_data_formatting($product);
            }
            $data[] = $banner;
        }*/
         $data[] = $banners;
        return response()->json($banners, 200);

    }
    
    public function transactions(Request $request)
    {
       
      $transction =   ServiceProviderPlan::with(['provider','membership'])->where('provider_id',$request->user()->id)->get();
      
       if ($transction->isNotEmpty()) {
            return response()->json([
                'message' => translate('service provider transactions'),
                'data' => $transction,
                'status' => true
               
            ], 200);
        } else {
            return response()->json([
                'message' => 'No data found',
                'data' => [],
                'status' => false
            ], 200);
        }
        
    }
    
     public function all_reviews(Request $request)
    {
       
      $all_reviews =   ProviderReviews::with(['customer:id,f_name,l_name,image'])->where('customer_id',$request->user()->id)->where('status',1)->where('provider_id',$request->provider_id)->get();
      
      $all_reviews = $all_reviews->map(function ($user) {
            // Format URLs
            $user->customer->image = asset('storage/profile/' . $user->customer->image);
           
            return $user;
        });
      
       if ($all_reviews->isNotEmpty()) {
            return response()->json([
                'message' => translate('service provider all all_reviews'),
                'data' => $all_reviews,
                'status' => true
               
            ], 200);
        } else {
            return response()->json([
                'message' => 'No data found',
                'data' => [],
                'status' => false
            ], 404);
        }
        
    }
    
     public function getUserData(Request $request)
    {
        $average_rating = 0;   
        $user = User::with('firm')
            ->where('id',$request->user()->id)
            ->first();
            
        $all_rating = ProviderReviews::where('provider_id',$request->id)->where('status',1)->get();
        if(!$all_rating->isNotEmpty()){
        $totalRating = $all_rating->sum('rating');
        $recordCount = $all_rating->count();
        if($recordCount){
        $average_rating = $totalRating/$recordCount;
        }else{
          $average_rating = 0;   
        }
        }
            
        if ($user != null) {
           
            $user->image = asset('storage/service-provider/profile/' . $user->image);
            $user->adhaar_front_image = asset('storage/service-provider/' . ($user->adhaar_front_image ?? ''));
            $user->adhaar_back_image = asset('storage/service-provider/' . ($user->adhaar_back_image ?? ''));
            $user->average_rating = $average_rating;
            
            return response()->json([
                'message' => translate('record'),
                'data' => $user,
                'status' => true
            ], 200);
        } else {
            return response()->json([
                'message' => 'data not found',
                'data' => (object)[],
                'status' => false
            ], 200);
        }
    }

}