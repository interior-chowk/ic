<?php

namespace App\Http\Controllers\api\v5;

use App\CPU\ImageManager;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use App\Model\Product;
use App\Model\ServiceCategory;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use App\Model\Membership;
use App\Model\ServiceProviderPlan;
use function App\CPU\translate;

class HomeController extends Controller
{
    
    
    public function category(Request $request)
    {
       
      $category =   ServiceCategory::select('id','name','slug','icon')->where('home_status',1)->where('parent_id',0)->get();
       
      $category = $category->map(function ($cat){
          $cat->icon = asset('storage/category/'.($cat->icon));
          return $cat;
      });
       if ($category->isNotEmpty()) {
            return response()->json([
                'message' => translate('service provider category'),
                'data' => $category,
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
   public function index(Request $request)
    {
        $longitude = $request->input('longitude');
        $latitude = $request->input('latitude');
        $radii = [10, 50]; // Radii to search in kilometers
        $data = [];
    
        foreach ($radii as $radius) {
            // Calculate boundaries for latitude and longitude
            $latBoundary = 0.009 * $radius;
            $lonBoundary = 0.009 * $radius / cos(deg2rad($latitude));
    
            // Define latitude and longitude range for filtering
            $minLat = $latitude - $latBoundary;
            $maxLat = $latitude + $latBoundary;
            $minLon = $longitude - $lonBoundary;
            $maxLon = $longitude + $lonBoundary;
    
            // Queries for each type of service provider featured
            $workerQuery = User::with('firm')->where('role', 2)->where('is_active',1)->where('featured',1)->orderBy('featured', 'desc')->orderBy('created_at', 'desc')
                ->whereBetween('latitude', [$minLat, $maxLat])
                ->whereBetween('longitude', [$minLon, $maxLon]);
    
            $contractorQuery = User::with('provider_plan','firm')->where('role', 3)->where('is_active',1)->where('featured',1)->orderBy('featured', 'desc')->orderBy('created_at', 'desc');
                /*->whereBetween('latitude', [$minLat, $maxLat])
                ->whereBetween('longitude', [$minLon, $maxLon]);*/
    
            $architectQuery = User::with('provider_plan','firm')->where('role', 4)->where('is_active',1)->where('featured',1)->orderBy('featured', 'desc')->orderBy('created_at', 'desc');
                /*->whereBetween('latitude', [$minLat, $maxLat])
                ->whereBetween('longitude', [$minLon, $maxLon]);*/
    
            $interiorDesignerQuery = User::with('provider_plan','firm')->where('role', 5)->where('is_active',1)->where('featured',1)->orderBy('featured', 'desc')->orderBy('created_at', 'desc');
                /*->whereBetween('latitude', [$minLat, $maxLat])
                ->whereBetween('longitude', [$minLon, $maxLon]);*/
    
            // Fetching and formatting results for each type of service provider
            $data['worker'] = $workerQuery->get()->map(function ($worker) {
                // Format URLs
                $worker->image = asset('storage/service-provider/profile/' . $worker->image);
                $worker->adhaar_front_image = asset('storage/service-provider/' . ($worker->adhaar_front_image ?? ''));
                $worker->adhaar_back_image = asset('storage/service-provider/' . ($worker->adhaar_back_image ?? ''));
                
                $isPlanValid = $this->checkPlanValidity($worker->id);
                $worker->ispaid = $isPlanValid;
                
                return $worker;
            });
            
             if (count($data['worker']) == 0) {
                $workerQuery = User::with('firm')->where('role', 2)->where('is_active', 1)->where('featured',1);
                
                $data['worker'] = $workerQuery->get();
                $data['worker'] = $workerQuery->get()->map(function ($worker) {
                $worker->image = asset('storage/service-provider/profile/' . $worker->image);
                $worker->adhaar_front_image = asset('storage/service-provider/' . ($worker->adhaar_front_image ?? ''));
                $worker->adhaar_back_image = asset('storage/service-provider/' . ($worker->adhaar_back_image ?? ''));
                $isPlanValid = $this->checkPlanValidity($worker->id);
                $worker->ispaid = $isPlanValid;
                
                return $worker;
                 });
             }
    
            $data['contractor'] = $contractorQuery->get()->map(function ($contractor) {
                // Format URLs
                $contractor->image = asset('storage/service-provider/profile/' . $contractor->image);
                $contractor->adhaar_front_image = asset('storage/service-provider/' . ($contractor->adhaar_front_image ?? ''));
                $contractor->adhaar_back_image = asset('storage/service-provider/' . ($contractor->adhaar_back_image ?? ''));
                
                $isPlanValid = $this->checkPlanValidity($contractor->id);
                $contractor->ispaid = $isPlanValid;
                
                return $contractor;
            });
            
            if (count($data['contractor']) == 0) {
                $contractorQuery = User::with('provider_plan','firm')->where('role', 3)->where('is_active', 1)->where('featured',1)->orderBy('featured', 'desc')->orderBy('created_at', 'desc');
                 
                $data['contractor'] = $contractorQuery->get();
    
                $data['contractor'] = $contractorQuery->get()->map(function ($contractor) {
                $contractor->image = asset('storage/service-provider/profile/' . $contractor->image);
                $contractor->adhaar_front_image = asset('storage/service-provider/' . ($contractor->adhaar_front_image ?? ''));
                $contractor->adhaar_back_image = asset('storage/service-provider/' . ($contractor->adhaar_back_image ?? ''));
                $isPlanValid = $this->checkPlanValidity($contractor->id);
                $contractor->ispaid = $isPlanValid;
                
                return $contractor;
                });
            }
    
            $data['architect'] = $architectQuery->get()->map(function ($architect) {
                // Format URLs
                $architect->image = asset('storage/service-provider/profile/' . $architect->image);
                $architect->adhaar_front_image = asset('storage/service-provider/' . ($architect->adhaar_front_image ?? ''));
                $architect->adhaar_back_image = asset('storage/service-provider/' . ($architect->adhaar_back_image ?? ''));
                
                $isPlanValid = $this->checkPlanValidity($architect->id);
                $architect->ispaid = $isPlanValid;
                
                return $architect;
            });
            
             if (count($data['architect']) == 0) {
                 
                 $architectQuery = User::with('provider_plan','firm')->where('role', 4)->where('is_active', 1)->where('featured',1)->orderBy('featured', 'desc')->orderBy('created_at', 'desc');
                 $data['architect'] = $architectQuery->get();
    
                $data['architect'] = $architectQuery->get()->map(function ($architect) {
                $architect->image = asset('storage/service-provider/profile/' . $architect->image);
                $architect->adhaar_front_image = asset('storage/service-provider/' . ($architect->adhaar_front_image ?? ''));
                $architect->adhaar_back_image = asset('storage/service-provider/' . ($architect->adhaar_back_image ?? ''));
                
                $isPlanValid = $this->checkPlanValidity($architect->id);
                $architect->ispaid = $isPlanValid;
                
                return $architect;
                });
                 
             }
    
            $data['interior_designer'] = $interiorDesignerQuery->get()->map(function ($interior_designer) {
                // Format URLs
                $interior_designer->image = asset('storage/service-provider/profile/' . $interior_designer->image);
                $interior_designer->adhaar_front_image = asset('storage/service-provider/' . ($interior_designer->adhaar_front_image ?? ''));
                $interior_designer->adhaar_back_image = asset('storage/service-provider/' . ($interior_designer->adhaar_back_image ?? ''));
                
                $isPlanValid = $this->checkPlanValidity($interior_designer->id);
                $interior_designer->ispaid = $isPlanValid;
                
                return $interior_designer;
            });
            
            if (count($data['interior_designer']) == 0) {
               $interiorDesignerQuery = User::with('provider_plan','firm')->where('role', 5)->where('is_active', 1)->where('featured',1)->orderBy('featured', 'desc')->orderBy('created_at', 'desc'); 
                $data['interior_designer'] = $interiorDesignerQuery->get();
    
                $data['interior_designer'] = $interiorDesignerQuery->get()->map(function ($interior_designer) {
                $interior_designer->image = asset('storage/service-provider/profile/' . $interior_designer->image);
                $interior_designer->adhaar_front_image = asset('storage/service-provider/' . ($interior_designer->adhaar_front_image ?? ''));
                $interior_designer->adhaar_back_image = asset('storage/service-provider/' . ($interior_designer->adhaar_back_image ?? ''));
                
                 $isPlanValid = $this->checkPlanValidity($interior_designer->id);
                $interior_designer->ispaid = $isPlanValid;
                
                return $interior_designer; 
                });
            }
    
            // Check if any data found
            if (!empty($data)) {
                return response()->json([
                    'data' => $data,
                    'status' => true
                ], 200);
            }
        }
    
        // If no data found for any radius
        return response()->json([
            'message' => 'No data found',
            'status' => false
        ], 404);
    }


    public function related_service_provider(Request $request)
    {
       
        
        $longitude = $request->input('longitude');
        $latitude = $request->input('latitude');
        $slug = $request->input('product_slug');
        $radii = [10, 50]; // Radii to search in kilometers
        $data = [];
        $tagsString = '';
        $ServiceTypes = '';
        $tag_name = [];
        $product = Product::with(['reviews.customer', 'seller.shop','tags'])->where(['slug' => $slug])->first();
        if($product){
        $tags = $product->tags;
         $ServiceTypes = json_decode($product->service_type);
        //$ServiceTypes = is_array($service_type) ? $service_type : explode(',', $service_type);
        if($tags){
         foreach ($tags as $tag){
             $tag_name[] = $tag->tag;
         }
          $tagsString = implode(', ', $tag_name);
         }
        }
         if($ServiceTypes){
        foreach ($radii as $radius) {
            // Calculate boundaries for latitude and longitude
            $latBoundary = 0.009 * $radius;
            $lonBoundary = 0.009 * $radius / cos(deg2rad($latitude));
    
            // Define latitude and longitude range for filtering
            $minLat = $latitude - $latBoundary;
            $maxLat = $latitude + $latBoundary;
            $minLon = $longitude - $lonBoundary;
            $maxLon = $longitude + $lonBoundary;
    
            // Queries for each type of service provider
            
            $workerQuery = User::with('firm')->where('role', 2)
                ->where('is_active', 1)->orderBy('featured', 'desc')->orderBy('created_at', 'desc')
                ->whereBetween('latitude', [$minLat, $maxLat])
                ->whereBetween('longitude', [$minLon, $maxLon])->get();
                
                $workerId = [];
                foreach ($workerQuery as $worker){
                   
                     $workerServiceTypes = json_decode($worker->serviceTypeId);
                   if (is_array($workerServiceTypes) && !empty(array_intersect($ServiceTypes, $workerServiceTypes))) {
                        $workerId[] = $worker->id;
                    }
                }
                $workerQuery =     User::whereIn('id',$workerId)->where('role', 2)
                ->where('is_active', 1)->orderBy('featured', 'desc')->orderBy('created_at', 'desc')
                ->whereBetween('latitude', [$minLat, $maxLat])
                ->whereBetween('longitude', [$minLon, $maxLon]);
    
               $contractorQuery = User::with('provider_plan','firm')->where('role', 3)
                ->where('is_active', 1)->orderBy('featured', 'desc')->orderBy('created_at', 'desc')->get();
                /*->whereBetween('latitude', [$minLat, $maxLat])
                ->whereBetween('longitude', [$minLon, $maxLon]);*/
                
                $contractorId = [];
               foreach ($contractorQuery as $contractor) {
                $contractorServiceTypes = json_decode($contractor->serviceTypeId);
                if (is_array($contractorServiceTypes) && !empty(array_intersect($ServiceTypes, $contractorServiceTypes))) {
                    $contractorId[] = $contractor->id;
                }
            }
               $contractorQuery =     User::with('provider_plan','firm')->where('role', 3)
                ->where('is_active', 1)->orderBy('featured', 'desc')->orderBy('created_at', 'desc')->whereIn('id',$contractorId);
    
               $architectQuery = User::with('provider_plan','firm')->where('role', 4)
                ->where('is_active', 1)->orderBy('featured', 'desc')->orderBy('created_at', 'desc')->get();
                /*->whereBetween('latitude', [$minLat, $maxLat])
                ->whereBetween('longitude', [$minLon, $maxLon]);*/
                
                 $architeachId = [];
                foreach ($architectQuery as $architect){
                   
                  $architectServiceTypes = json_decode($architect->serviceTypeId);
               if (is_array($architectServiceTypes) && !empty(array_intersect($ServiceTypes, $architectServiceTypes))) {
                    $architeachId[] = $architect->id;
                }
                }
                 $architectQuery =     User::with('provider_plan','firm')->where('role', 4)
                ->where('is_active', 1)->orderBy('featured', 'desc')->orderBy('created_at', 'desc')->whereIn('id',$architeachId);
    
                $interiorDesignerQuery = User::with('provider_plan','firm')->where('role', 5)
                ->where('is_active', 1)->orderBy('featured', 'desc')->orderBy('created_at', 'desc')->get();
               /* ->whereBetween('latitude', [$minLat, $maxLat])
                ->whereBetween('longitude', [$minLon, $maxLon]);*/
                
                 $adesignerId = [];
                foreach ($interiorDesignerQuery as $interiorDesigner){
                   
                     $interiorDesignerServiceTypes = json_decode($interiorDesigner->serviceTypeId);
                   if ( is_array($interiorDesignerServiceTypes) && !empty(array_intersect($ServiceTypes, $interiorDesignerServiceTypes))) {
                        $adesignerId[] = $interiorDesigner->id;
                    }
                
                
                }
                $interiorDesignerQuery =     User::with('provider_plan','firm')->where('role', 5)
                ->where('is_active', 1)->orderBy('featured', 'desc')->orderBy('created_at', 'desc')->whereIn('id',$adesignerId);
    
            // Fetching and formatting results for each type of service provider
            $data['worker'] = $workerQuery->get()->map(function ($worker) {
                // Format URLs
                
                
                $worker->image = asset('storage/service-provider/profile/' . $worker->image);
                $worker->adhaar_front_image = asset('storage/service-provider/' . ($worker->adhaar_front_image ?? ''));
                $worker->adhaar_back_image = asset('storage/service-provider/' . ($worker->adhaar_back_image ?? ''));
                return $worker;
            });
            
            /* if (count($data['worker']) == 0) {
                $workerQuery = User::with('firm')->where('role', 2)->where('is_active',1);
                
                $data['worker'] = $workerQuery->get();
                $data['worker'] = $workerQuery->get()->map(function ($worker) {
                $worker->image = asset('storage/service-provider/profile/' . $worker->image);
                $worker->adhaar_front_image = asset('storage/service-provider/' . ($worker->adhaar_front_image ?? ''));
                $worker->adhaar_back_image = asset('storage/service-provider/' . ($worker->adhaar_back_image ?? ''));
                return $worker;
                 });
             }*/
    
            $data['contractor'] = $contractorQuery->get()->map(function ($contractor) {
                // Format URLs
                $contractor->image = asset('storage/service-provider/profile/' . $contractor->image);
                $contractor->adhaar_front_image = asset('storage/service-provider/' . ($contractor->adhaar_front_image ?? ''));
                $contractor->adhaar_back_image = asset('storage/service-provider/' . ($contractor->adhaar_back_image ?? ''));
                return $contractor;
            });
            
           /* if (count($data['contractor']) == 0) {
                $contractorQuery = User::with('firm')->where('role', 3)->where('is_active',1);
                 
                $data['contractor'] = $contractorQuery->get();
    
                $data['contractor'] = $contractorQuery->get()->map(function ($contractor) {
                $contractor->image = asset('storage/service-provider/profile/' . $contractor->image);
                $contractor->adhaar_front_image = asset('storage/service-provider/' . ($contractor->adhaar_front_image ?? ''));
                $contractor->adhaar_back_image = asset('storage/service-provider/' . ($contractor->adhaar_back_image ?? ''));
                return $contractor;
                });
            }*/
    
            $data['architect'] = $architectQuery->get()->map(function ($architect) {
                // Format URLs
                $architect->image = asset('storage/service-provider/profile/' . $architect->image);
                $architect->adhaar_front_image = asset('storage/service-provider/' . ($architect->adhaar_front_image ?? ''));
                $architect->adhaar_back_image = asset('storage/service-provider/' . ($architect->adhaar_back_image ?? ''));
                return $architect;
            });
            
            /* if (count($data['architect']) == 0) {
                 
                 $architectQuery = User::with('firm')->where('role', 4)->where('is_active',1);
                 $data['architect'] = $architectQuery->get();
    
                $data['architect'] = $architectQuery->get()->map(function ($architect) {
                $architect->image = asset('storage/service-provider/profile/' . $architect->image);
                $architect->adhaar_front_image = asset('storage/service-provider/' . ($architect->adhaar_front_image ?? ''));
                $architect->adhaar_back_image = asset('storage/service-provider/' . ($architect->adhaar_back_image ?? ''));
                return $architect;
                });
                 
             }*/
    
            $data['interior_designer'] = $interiorDesignerQuery->get()->map(function ($interior_designer) {
                // Format URLs
                $interior_designer->image = asset('storage/service-provider/profile/' . $interior_designer->image);
                $interior_designer->adhaar_front_image = asset('storage/service-provider/' . ($interior_designer->adhaar_front_image ?? ''));
                $interior_designer->adhaar_back_image = asset('storage/service-provider/' . ($interior_designer->adhaar_back_image ?? ''));
                return $interior_designer;
            });
            
           /* if (count($data['interior_designer']) == 0) {
               $interiorDesignerQuery = User::with('firm')->where('role', 5)->where('is_active',1); 
                $data['interior_designer'] = $interiorDesignerQuery->get();
    
                $data['interior_designer'] = $interiorDesignerQuery->get()->map(function ($interior_designer) {
                $interior_designer->image = asset('storage/service-provider/profile/' . $interior_designer->image);
                $interior_designer->adhaar_front_image = asset('storage/service-provider/' . ($interior_designer->adhaar_front_image ?? ''));
                $interior_designer->adhaar_back_image = asset('storage/service-provider/' . ($interior_designer->adhaar_back_image ?? ''));
                return $interior_designer; 
                });
            }*/
             $data['worker'] = [];
            // Check if any data found
            if (!empty($data)) {
                return response()->json([
                    'data' => $data,
                    'status' => true
                ], 200);
            }
        }
         }
        // If no data found for any radius
        return response()->json([
            'message' => 'No record found',
            'status' => false
        ], 200);
    }
    
    public function checkPlanValidity($providerId)
    {
        $plan = '';
        $verified = 0;

        // Retrieve the latest plan purchase
        $planPurchase = ServiceProviderPlan::where('provider_id', $providerId)->latest()->first();

        if ($planPurchase) {
            $plan = Membership::find($planPurchase->membership_id);

            if ($plan) {
                $validity = $plan->validity == 'monthly' ? 30 : ($plan->validity == 'yearly' ? 365 : 0);

                if ($validity > 0) {
                    $createdAt = Carbon::parse($planPurchase->created_at);
                    $expiryDate = $createdAt->addDays($validity);
                    $currentDateTime = Carbon::now();

                    if ($expiryDate >= $currentDateTime) {
                        $verified = 1;
                    }
                }
            }
        }

        return $verified;
    }
}