<?php

namespace App\Http\Controllers\api\v5;

use App\CPU\ImageManager;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;
use App\Model\ProviderWalletHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use function App\CPU\translate;
use App\Model\FavouriteProviderList;
use App\Model\ProviderReviews;
use App\Model\ServiceCategory;
use App\Model\ProviderGallery;
use App\Model\Membership;
use App\Traits\CommonTrait;


class ServiceProviderController extends Controller
{
     use CommonTrait;
    public function info(Request $request)
    {
        return response()->json($request->user(), 200);
    }
    
    public function account_delete(Request $request, $id)
    {
        if($request->user()->id == $id)
        {
            $user = User::find($id);
            
            if($user['image']){
            ImageManager::delete('/service-provider/profile/' . $user['image']);
            }
            if($user['adhaar_front_image']){
            ImageManager::delete('/service-provider/' . $user['adhaar_front_image']);
            }
            if($user['adhaar_back_image']){
            ImageManager::delete('/service-provider/' . $user['adhaar_back_image']);
            }

            $user->delete();
           return response()->json(['message' => translate('Your_account_deleted_successfully!!')],200);

        }else{
            return response()->json(['message' =>'access_denied!!'],403);
        }
    }
    
    public function details_by_id(Request $request)
    {
        $average_rating = 0;
        $user = User::with('firm','provider_gallery')->where('id', $request->id)->whereIn('role', [2,3,4,5])->where('is_active',1)->get();
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
            $user = $user->map(function ($user) use ($average_rating) {
                // Format URLs
                $user->image = asset('storage/service-provider/profile/' . $user->image);
                $user->adhaar_front_image = asset('storage/service-provider/' . ($user->adhaar_front_image ?? ''));
                $user->adhaar_back_image = asset('storage/service-provider/' . ($user->adhaar_back_image ?? ''));
                $user->average_rating = $average_rating;
                return $user;
            });
            
            return response()->json([
                'message' => translate('service provider details'),
                'data' => $user,
                'status' => true
            ], 200);
        } else {
            return response()->json([
                'message' => 'not found data',
                'data' => (object)[],
                'status' => false
            ], 200);
        }
    }

    
    public function add_favourite(Request $request)
    {
      
         $record = FavouriteProviderList::where('customer_id', $request->user()->id)->where('provider_id', $request->provider_id)->first();
         
         if ($record == null) {
             
             $res = new FavouriteProviderList();
             $res->customer_id = $request->user()->id;
             $res->provider_id = $request->provider_id;
             $res->save();
             
              return response()->json([
                'message' => translate('successfully added to favourite'),
                'status' => true
            ], 200);
        } else {
            FavouriteProviderList::where('customer_id', $request->user()->id)->where('provider_id', $request->provider_id)->delete();
            return response()->json([
                'message' => translate('removed from favourite list !'),
                'status' => false
            ], 200);
        }
    }
    
    public function add_review(Request $request)
    {
      
         $record = ProviderReviews::where('customer_id', $request->user()->id)->where('provider_id', $request->provider_id)->first();
         
         if ($record == null) {
             
             $res = new ProviderReviews();
             $res->customer_id = $request->user()->id;
             $res->provider_id = $request->provider_id;
             $res->rating = $request->rating;
             $res->comment = $request->comment;
             $res->save();
             
              return response()->json([
                'message' => translate('successfully added reviews'),
                'status' => true
            ], 200);
        } else {
            return response()->json([
                'message' => translate('Already added !'), 
                'status' => false
            ], 200);
        }
    }
    
    public function all_views(Request $request)
    {
        $allCity = [];
        $allServiceType = [];
        $average_rating = 0;   
        $role = $request->input('service_provider_type', null);
        $radius = $request->input('radius', null);
        $reviewRating = $request->input('review', null);
        $service_type = $request->input('service_type', null);
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $search = $request->input('search', null);
        $experience = $request->input('experience', null);
        $noOfProjectsDone = $request->input('noOfProjectsDone', null);
        $teamStrength = $request->input('teamStrength', null);
        $city = $request->input('city', null);
        $trustedPartner = $request->input('trustedPartner', null);
        if($teamStrength){
        list($minTeamStrength, $maxTeamStrength) = explode('-', $teamStrength);
        }
        
        if($experience){
        list($minExperience, $maxExperience) = explode('-', $experience);
      
        $currentDate = Carbon::now();
        $mincalculatedDate = $currentDate->subYears($minExperience);
        $minPastYear = $mincalculatedDate->format('Y-m-d');
        
        $maxcalculatedDate = $currentDate->subYears($maxExperience);
        $maxPastYear = $maxcalculatedDate->format('Y-m-d');
    
        }
        
        if($service_type){
          $service_types = array_map('trim', explode(',', $service_type));
          
        }
        
        if($city){
          $citys = array_map('trim', explode(',', $city));
          
        }
    
        $query = User::with(['firm','reviews','provider_plan'])->where('is_active',1);
    
        if ($role !== null) {
            $query->where('role', $role);
        }else{
           $query->whereIn('role', [2,3,4,5]); 
        }
        
        if ($radius !== null) {
            $query->where('radius_of_working_area_in_mm', $radius);
        }
        
        if ($noOfProjectsDone !== null) {
            $query->where('total_project_done', $noOfProjectsDone);
        }
        
         if ($teamStrength !== null) {
             $query->whereBetween('team_strength', [$minTeamStrength, $maxTeamStrength]);
        }
        
        if ($experience !== null) {
             $query->where('working_since', '>=', $minPastYear)
                   ->where('working_since', '<=', $maxPastYear);
        }
        
         if ($search !== null) {
            $query->where('name', 'like', '%' . $search . '%')
          ->orWhere('role_name', 'like', '%' . $search . '%');
        }
        
         if ($city !== null) {
            foreach ($citys as $cit) {
            $query = $query->whereRaw("city LIKE ?", ["%$cit%"]);
            }
        }
        
         if ($service_type !== null) {
            // return $service_type;
            //$query = $query->whereRaw("type_of_work LIKE ?", ["%$service_type%"]);
            foreach ($service_types as $id) {
              $query->WhereRaw("serviceTypeId LIKE ?", ["%\"$id\"%"]);
            }
        }
        
        if ($reviewRating !== null) {
            
            $query->whereHas('reviews', function ($q) use ($reviewRating) {
                $q->where('provider_reviews.rating', '<=', $reviewRating)->where('provider_reviews.status',1);
            });
           
        }
        
        if ($trustedPartner) {
            $query->whereHas('provider_plan', function ($q) use ($trustedPartner) {
                $q->orderBy('service_provider_plans.amount','desc');
            });
           
        }
        
    
        // Apply pagination
        $totalUsers = $query->count();
        $query->skip(($page - 1) * $limit)->take($limit);
    
        $users = $query->orderBy('featured', 'desc')->orderBy('created_at', 'desc')->get();
        
         $users = $users->map(function ($users) {
                // Format URLs
                $users->image = asset('storage/service-provider/profile/' . $users->image);
                $users->adhaar_front_image = asset('storage/service-provider/' . ($users->adhaar_front_image ?? ''));
                $users->adhaar_back_image = asset('storage/service-provider/' . ($users->adhaar_back_image ?? ''));
        
             if ($users->provider_plan->isNotEmpty()) {
                    $memPlans =  $users->provider_plan;
                     $verified = 0;
                   foreach ($memPlans as $memPlan) {
                       
                     $plan = Membership::where('id', $memPlan['membership_id'])->first();
                     if($plan){
                         if($plan->validity == 'monthly')
                                {
                                $validity = 30;
                                $memPlan->created_at;
                                $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
                                $createdAt = Carbon::parse($memPlan->created_at);
                               
                                $expiryDate = $createdAt->addDays($validity);
                                
                                if($expiryDate >= $currentDateTime){
                                $verified = 1;
                               
                                }
                                
                                }
                                if($plan->validity == 'yearly'){
                                $validity = 365; 
                                $memPlan->created_at;
                                $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
                                $createdAt = Carbon::parse($memPlan->created_at);
                                
                                $expiryDate = $createdAt->addDays($validity);
                                if($expiryDate >= $currentDateTime){
                                $verified = 1;
                                }
                                }
                     }
                            $users->ispaid = $verified;
                        
                   }
                }
        
                return $users;
            });
            
            foreach ($users as $user) {
                
                $all_rating = ProviderReviews::where('provider_id', $user->id)
                    ->where('status', 1)
                    ->get();
                
                if ($all_rating->isNotEmpty()) {
                    $totalRating = $all_rating->sum('rating');
                    $recordCount = $all_rating->count();
                    $average_rating = $recordCount ? $totalRating / $recordCount : 0;
                } else {
                    $average_rating = 0;
                }
                
                $user->average_rating = $average_rating;
                
                if($user->city){
                $allCity[] = $user->city;
                 }
            }
            $allServiceType = ServiceCategory::select('id','name')->where('parent_id',0)->where('home_status',1)->get();
            
            $cities = is_array($allCity) ? $allCity : json_decode($allCity, true);
            $allCitiest = [] ; 
                foreach ($cities as $cityGroup) {
                    
                    $cityArray = array_map('trim', explode(',', trim($cityGroup, '[]')));
        
                    
                    $cityArray = array_unique($cityArray);
        
                    
                    $allCitiest[] = implode(', ', $cityArray);
                }
                
                $allCities = [];

                foreach ($allCitiest as $cityGroup) {
                   
                    $cityArray = array_map('trim', explode(',', $cityGroup));
                    
                   
                    $allCities = array_merge($allCities, $cityArray);
                }
                
                
               $uniqueCities = array_unique($allCities);
               
             $cleanedCityString = array_values($uniqueCities);
      
           $allOptions = ['service_type' => $allServiceType,'city' =>$cleanedCityString];
        
    
        if ($users->isNotEmpty()) {
            return response()->json([
                'message' => translate('service provider details'),
                'data' => $users,
                'allOptions' => $allOptions,
                'status' => true,
                'total_users' => $totalUsers
            ], 200);
        } else {
            return response()->json([
                'message' => 'No data found',
                'data' => [],
                'status' => false
            ], 404);
        }
    }
    
     public function related_constructor(Request $request)
    {
       
        $worker_id = $request->input('worker_id', null);
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $res = User::where('id',$worker_id)->where('is_active',1)->first();
        
        if($res)
        {
        $service_type = $res->type_of_work;
       
        $query = User::with(['firm','provider_plan'])->whereIn('role', [2,3,4,5])->whereRaw("type_of_work LIKE ?", ["%$service_type%"])->where('id', '!=', $worker_id)->where('is_active',1);
    
       
        // Apply pagination
        $totalUsers = $query->count();
        $query->skip(($page - 1) * $limit)->take($limit);
    
        $users = $query->get();
        
       
        
         $users = $users->map(function ($users) {
                // Format URLs
                $users->image = asset('storage/service-provider/profile/' . $users->image);
                $users->adhaar_front_image = asset('storage/service-provider/' . ($users->adhaar_front_image ?? ''));
                $users->adhaar_back_image = asset('storage/service-provider/' . ($users->adhaar_back_image ?? ''));
                return $users;
            });
        
    
        if ($users->isNotEmpty()) {
            return response()->json([
                'message' => translate('service provider details'),
                'data' => $users,
                'status' => true,
                'total_users' => $totalUsers
            ], 200);
        } else {
            return response()->json([
                'message' => 'No data found',
                'data' => [],
                'status' => false
            ], 404);
        }
        }
        else{
             return response()->json([
                'message' => 'No data found',
                'data' => [],
                'status' => false
            ], 404);
        }
    }
    
     public function all_favorite_provider(Request $request)
    {
       
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
    
        $query = FavouriteProviderList::with(['serviceProvider','firm'])->where('customer_id', $request->user()->id);
    
        // Apply pagination
        $totalUsers = $query->count();
        $users = $query->skip(($page - 1) * $limit)->take($limit)->get();
        
        $users = $users->filter(function ($user) {
            return !is_null($user->serviceProvider);
        })->values();
    
        $formattedUsers = $users->map(function ($user) {
            // Format URLs
           if ($user->serviceProvider) {
        $user->serviceProvider->image = asset('storage/service-provider/profile/' . $user->serviceProvider->image);
        $user->serviceProvider->adhaar_front_image = asset('storage/service-provider/' . ($user->serviceProvider->adhaar_front_image ?? ''));
        $user->serviceProvider->adhaar_back_image = asset('storage/service-provider/' . ($user->serviceProvider->adhaar_back_image ?? ''));
         }
            return $user;
        });
        
        return $formattedUsers;
    
        if ($formattedUsers->isNotEmpty()) {
            return response()->json([
                'message' => translate('favourite service provider details'),
                'data' => $formattedUsers,
                'status' => true,
                'total_users' => $totalUsers
            ], 200);
        } else {
            return response()->json([
                'message' => 'No data found',
                'data' => [],
                'status' => false
            ], 404);
        }
    }
    
     public function all_favorite_review(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
    
        $query = ProviderReviews::with(['customer:id,f_name,l_name,image','serviceProvider','firm'])->where('customer_id', $request->user()->id)->where('status',1)->where('provider_id',$request->provider_id);
    
        // Apply pagination
        $totalUsers = $query->count();
        $users = $query->skip(($page - 1) * $limit)->take($limit)->get();
    
        $formattedUsers = $users->map(function ($user) {
            // Format URLs
            $user->customer->image = asset('storage/profile/' . $user->customer->image);
            $user->serviceProvider->image = asset('storage/service-provider/profile/' . $user->serviceProvider->image);
            $user->serviceProvider->adhaar_front_image = asset('storage/service-provider/' . ($user->serviceProvider->adhaar_front_image ?? ''));
            $user->serviceProvider->adhaar_back_image = asset('storage/service-provider/' . ($user->serviceProvider->adhaar_back_image ?? ''));
            return $user;
        });
    
        if ($formattedUsers->isNotEmpty()) {
            return response()->json([
                'message' => translate('favourite service provider details'),
                'data' => $formattedUsers,
                'status' => true,
                'total_users' => $totalUsers
            ], 200);
        } else {
            return response()->json([
                'message' => 'No data found',
                'data' => [],
                'status' => false
            ], 404);
        }
    }
    
    public function wallet_history(Request $request)
    {
       
      $wallet =   ProviderWalletHistory::where('provider_id',$request->user()->id)->get();
       $totalTransactionAmount = User::where('id', $request->user()->id)->sum('wallet_balance');
      
       if ($wallet->isNotEmpty()) {
            return response()->json([
                'message' => translate('service provider wallet'),
                'wallet_balance' => $totalTransactionAmount,
                'data' => $wallet,
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
    
     public function add_gallery(Request $request)
    {
     
        $project_names_array = explode(',', $request->project_names);
        $links = explode(',', $request->links);
        
       $count = count($project_names_array);
        
        for ($i = 0; $i < $count; $i++) {
             $record = new ProviderGallery();
            $record->provider_id = $request->provider_id;
            $record->name = $project_names_array[$i];
            $record->links = $links[$i];
            $record->save();
        }
     
            return response()->json([
                'message' => translate('record add successfully'),
                'status' => true
               
            ], 200);
       
        
    }
    
     public function get_gallery_data(Request $request)
    {
       
      $ProviderGallery =   ProviderGallery::where('provider_id',$request->provider_id)->get();
       
      
       if ($ProviderGallery->isNotEmpty()) {
            return response()->json([
                'message' => translate('service provider gallery record'),
                'data' => $ProviderGallery,
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
    
    public function project_delete(Request $request)
    {
       
        $ProviderGallery =   ProviderGallery::where('id',$request->id)->where('provider_id',$request->provider_id)->first();
       if($ProviderGallery)
        {
            $ProviderGallery->delete();
           return response()->json(['message' => translate('Your_project_deleted_successfully!!')],200);

        }else{
            return response()->json(['message' =>'access_denied!!'],403);
        }
    }


}