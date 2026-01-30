<?php

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Model\Gst;
use Illuminate\Support\Facades\Log;
use Exception;

class GstController extends Controller
{
    public function get_token(Request $request)
    {
        $client_id = env('GST_CLIENT_ID');
        $client_secret = env('GST_CLIENT_SECRET');
        $auth = base64_encode($client_id . ':' . $client_secret);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $auth,
                'Content-Type' => 'application/json',
            ])->post('https://devapi.gst.gov.in/authenticate');

            if ($response->successful()) {
                $data = $response->json();
                return response()->json(['token' => $data['access_token']], 200);
            } else {
                return response()->json(['error' => 'Failed to get token'], $response->status());
            }
        } catch (Exception $e) {
            Log::error('Error fetching GST token: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function search_gst(Request $request)
    {
        $gstin = $request->input('gstin');
        $token = $request->input('token');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->get("https://devapi.gst.gov.in/taxpayerapi/v0.3/taxpayers/$gstin");

            if ($response->successful()) {
                $data = $response->json();
                return response()->json($data, 200);
            } else {
                return response()->json(['error' => 'Failed to fetch GST details'], $response->status());
            }
        } catch (Exception $e) {
            Log::error('Error fetching GST details: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}