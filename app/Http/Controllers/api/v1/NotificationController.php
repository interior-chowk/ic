<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Model\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function get_notifications()
    {
        try {
            return response()->json(Notification::active()->orderBy('id','DESC')->get(), 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }
    
     public function mark_notifications(Request $request )
    {
        try {
            return response()->json(Notification::active()->where('flag',$request->flag)->orderBy('id','DESC')->get(), 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }
    
    public function read_notifications(Request $request)
    {
        $found = Notification::find($request->id);
        if($found){
        Notification::where('id', $request->id)->update([
                                                            'flag' => 0,
                                                            'flag_status' => 'read',
                                                        ]);
        return response()->json(['message' => translate('update_successfully!!')],200);
        }else{
            return response()->json(['message' =>'no record found!!'],403);
        }
    }
}