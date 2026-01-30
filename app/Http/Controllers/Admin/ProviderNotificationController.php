<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\ProviderNotification;
use Brian2694\Toastr\Facades\Toastr;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\FirebaseServices\FirebaseNotificationService;

class ProviderNotificationController extends Controller
{
    
    protected $firebaseNotificationService;

    public function __construct(FirebaseNotificationService $firebaseNotificationService)
    {
        $this->firebaseNotificationService = $firebaseNotificationService; 
    }
    
    public function index(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search'))
        {
            $key = explode(' ', $request['search']);
            $notifications = ProviderNotification::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('title', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }else{
            $notifications = new ProviderNotification();
        }
        $notifications = $notifications->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.provider-notification.index', compact('notifications','search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required'
        ], [
            'title.required' => 'title is required!',
        ]);

        $notification = new ProviderNotification;
        $notification->title = $request->title;
        $notification->description = $request->description;
        $notification->sub_category = $request->sub_category;
        $notification->sub_sub_category = $request->sub_sub_category;
        $notification->resource_type = $request->resource_type;
        $notification->resource_id = $request[$request->resource_type . '_id'];

        if ($request->has('image')) {
            $notification->image = ImageManager::upload('notification/', 'png', $request->file('image'));
        } else {
            $notification->image = 'null';
        }

        $notification->status             = 1;
        $notification->notification_count = 1;
        $notification->save();
        
         try {
            
               $image = asset('storage/app/public/notification') . '/' . $notification->image;
    
           
            $notificationData = [
                'title' => $notification->title,
                'body' => $notification->description,
                'data' => [
                    'image' => $image,
                ],
            ];
            
            $this->firebaseNotificationService->sendNotificationToAllProviders($notificationData);
            Toastr::success(\App\CPU\translate("Notification sent successfully!"));
            return back();
    
        } catch (\Exception $e) {
            Toastr::error(\App\CPU\translate("Push notification failed!"));
            return back();
        }

    }

    public function edit($id)
    {
        $notification = ProviderNotification::find($id);
        return view('admin-views.provider-notification.edit', compact('notification'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ], [
            'title.required' => 'title is required!',
        ]);

        $notification = ProviderNotification::find($id);
        $notification->title = $request->title;
        $notification->description = $request->description;
        $notification->sub_category = $request->sub_category_id;
        $notification->sub_sub_category = $request->sub_sub_category_id;
        $notification->resource_type = $request->resource_type;
        $notification->resource_id = $request[$request->resource_type . '_id'];
        $notification->image = $request->has('image')? ImageManager::update('notification/', $notification->image, 'png', $request->file('image')):$notification->image;
        $notification->save();

        Toastr::success('Notification updated successfully!');
        return redirect('/admin/provider-notification/add-new');
    }

    public function status(Request $request)
    {
        if ($request->ajax()) {
            $notification = ProviderNotification::find($request->id);
            $notification->status = $request->status;
            $notification->save();
            $data = $request->status;
            return response()->json($data);
        }
    }
    
    public function resendNotification(Request $request)
    {
        $notification = ProviderNotification::find($request->id);
    
        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => \App\CPU\translate("Notification not found!"),
            ], 404);
        }
    
        try {
            // Set up image URL
            $image = asset('storage/app/public/notification') . '/' . $notification->image;
    
            // Prepare notification data
            $notificationData = [
                'title' => $notification->title,
                'body' => $notification->description,
                'data' => [
                    'image' => $image,
                ],
            ];
    
            // Send notification to all users
            $result = $this->firebaseNotificationService->sendNotificationToAllProviders($notificationData);
    
            // Increment notification count
            $notification->notification_count += 1;
            $notification->save();
    
            return response()->json([
                'success' => true,
                'message' => \App\CPU\translate("Push notification sent successfully!"),
                'result' => $result,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => \App\CPU\translate("Push notification failed!"),
                'error' => $e->getMessage(), // Optionally, include the exception message for debugging
            ], 500);
        }
    }

    public function resendNotification_old(Request $request)
    {
        $notification = ProviderNotification::find($request->id);

        $data = array();
        try {
            $users = User::whereIn('role', [2,3,4,5])->get();

            foreach ($users as $user) {
                if ($user->cm_firebase_token) {
                    Helpers::send_push_notif_to_topic($notification, $user->cm_firebase_token);
                }
            }
            //Helpers::send_push_notif_to_topic($notification);
            $notification->notification_count += 1;
            $notification->save();

            $data['success'] = true;
            $data['message'] = \App\CPU\translate("Push notification successfully!");
        } catch (\Exception $e) {
            $data['success'] = false;
            $data['message'] = \App\CPU\translate("Push notification failed!");
        }

        return $data;
    }

    public function delete(Request $request)
    {
        $notification = ProviderNotification::find($request->id);
        ImageManager::delete('/notification/' . $notification['image']);
        $notification->delete();
        return response()->json();
    }
}