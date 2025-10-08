<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\Notification;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\FirebaseServices\FirebaseNotificationService;

class NotificationController extends Controller
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
            $notifications = Notification::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('title', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }else{
            $notifications = new Notification();
        }
        $notifications = $notifications->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.notification.index', compact('notifications','search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required'
        ], [
            'title.required' => 'title is required!',
        ]);

        $notification = new Notification;
        $notification->title = $request->title;
        $notification->description = $request->description;

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
            
            $this->firebaseNotificationService->sendNotificationToAllUsers($notificationData);
            Toastr::success(\App\CPU\translate("Notification sent successfully!"));
            return back();
    
        } catch (\Exception $e) {
            Toastr::error(\App\CPU\translate("Push notification failed!"));
            return back();
        }
    }

    public function edit($id)
    {
        $notification = Notification::find($id);
        return view('admin-views.notification.edit', compact('notification'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ], [
            'title.required' => 'title is required!',
        ]);

        $notification = Notification::find($id);
        $notification->title = $request->title;
        $notification->description = $request->description;
        $notification->image = $request->has('image')? ImageManager::update('notification/', $notification->image, 'png', $request->file('image')):$notification->image;
        $notification->save();

        Toastr::success('Notification updated successfully!');
        return redirect('/admin/notification/add-new');
    }

    public function status(Request $request)
    {
        if ($request->ajax()) {
            $notification = Notification::find($request->id);
            $notification->status = $request->status;
            $notification->save();
            $data = $request->status;
            return response()->json($data);
        }
    }
    
    public function resendNotification_old(Request $request){
        $notification = Notification::find($request->id);
        $data = array();
        
        try {
            $users = User::where('role', NULL)->get();
            $notification->notification_count += 1; // Move this here to always increment
    
            foreach ($users as $user) {
                if ($user->cm_firebase_token) {
                    \Log::info('Sending notification to token: ' . $user->cm_firebase_token); // Log the token
                   return $result = Helpers::send_push_notif_to_device_noti($notification, $user->cm_firebase_token);
                    
                    if (!$result['success']) {
                        \Log::error('Failed to send notification: ' . $result['message']);
                    }
                }
            }
    
            $notification->save();
            $data['success'] = true;
            $data['message'] = \App\CPU\translate("Push notification successfully!");
        } catch (\Exception $e) {
            $data['success'] = false;
            $data['message'] = \App\CPU\translate("Push notification failed!");
        }
    
        return $data;
    }
    
    /*public function resendNotificationdemo(Request $request){
        
        $notification = Notification::find($request->id);
        $data = array();
         try {
        $image = asset('storage/app/public/notification') . '/' . $notification->image;
        
        $notificationData = [
            'title' => $notification->title,
            'body' =>  $notification->description,
            'data' => [
            'image' => $image, 
            ],
        ];

    
        return $data;
        
        return response()->json($result);
    }*/
    
    public function resendNotification(Request $request)
    {
        $notification = Notification::find($request->id);
    
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
            $result = $this->firebaseNotificationService->sendNotificationToAllUsers($notificationData);
    
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

    

    public function delete(Request $request)
    {
        $notification = Notification::find($request->id);
        ImageManager::delete('/notification/' . $notification['image']);
        $notification->delete();
        return response()->json();
    }
}
