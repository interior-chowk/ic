<?php

namespace App\CPU;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ImageManager
{
    public static function upload(string $dir, string $format, $image = null)
    {
       
        if ($image != null) {
            $imageName = Carbon::now()->toDateString() . "-" . uniqid() . "." . $format;
            if (!Storage::disk('public')->exists($dir)) {
              
                Storage::disk('public')->makeDirectory($dir);
            }
            Storage::disk('public')->put($dir . $imageName, file_get_contents($image));
        } else {
            $imageName = 'def.png';
        }

        return $imageName;
    }

    public static function uploads(string $dir, string $format, $file = null)
    {
        if ($file != null) {
           
            $videoName = Carbon::now()->toDateString() . "-" . uniqid() . "." . $format;
    
           
            if (!Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->makeDirectory($dir);
            }
    
           
            Storage::disk('public')->put($dir . $videoName, file_get_contents($file));
        } else {
            
            $videoName = 'default.mp4';
        }

        return $videoName;
    }
    

    public static function uploadss(string $dir, string $format, $file = null) 
{
    if ($file != null) {
        $fileName = Carbon::now()->toDateString() . "-" . uniqid() . "." . $format;
        
        if (!Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->makeDirectory($dir);
        }
        
        Storage::disk('public')->put($dir . $fileName, file_get_contents($file));
    } else {
        $fileName = null; // Default PDF if no file is uploaded
    }

    return $fileName;
}

    public static function update(string $dir, $old_image, string $format, $image = null)
    {
        if (Storage::disk('public')->exists($dir . $old_image)) {
            Storage::disk('public')->delete($dir . $old_image);
        }
        $imageName = ImageManager::upload($dir, $format, $image);
        return $imageName;
    }

    public static function delete($full_path)
    {
        if (Storage::disk('public')->exists($full_path)) {
            Storage::disk('public')->delete($full_path);
        }

        return [
            'success' => 1,
            'message' => 'Removed successfully !'
        ];

    }
}
