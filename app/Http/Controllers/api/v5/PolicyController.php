<?php

namespace App\Http\Controllers\api\v5;

use App\CPU\ImageManager;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use function App\CPU\translate;
use App\Model\ProviderHelpTopic;
use App\Traits\CommonTrait;
use App\Model\HelpTopic;
use App\Model\HelpTopicSubCategory;

class PolicyController extends Controller
{
     use CommonTrait;
    public function info(Request $request)
    {
        return response()->json($request->user(), 200);
    }

    public function faq(Request $request)
    {
        $subcategories = HelpTopicSubCategory::with(['faqs' => function($q) {
            $q->where('status', 1);
        }])->get();

        $faqsWithoutCategory = HelpTopic::where('status', 1)
            ->where(function ($query) {
                $query->whereNull('category_id')
                    ->orWhereNull('sub_cat_id');
            })
            ->get();

        return response()->json([
            'message' => translate('faq details'),
            'data' => [
                'categorized' => $subcategories,
                'uncategorized' => $faqsWithoutCategory,
            ],
            'status' => true,
        ], 200);
    }



}