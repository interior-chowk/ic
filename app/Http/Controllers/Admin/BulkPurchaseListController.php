<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\Admin;
use App\Model\BulkPurchaseList;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\Translation;
use Rap2hpoutre\FastExcel\FastExcel;

class BulkPurchaseListController extends Controller
{

    function list(Request $request)
    {
         $search = $request->get('search', '');

        // Fetch bulk purchase list with related user and product
       $br = BulkPurchaseList::with(['user'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('f_name', 'like', "%$search%");
                })
                ->orWhere('product_name', 'like', "%$search%"); // search on BulkPurchaseList's own 'name' column
            })
            ->orderBy('id', 'desc')
            ->paginate(10);



        return view('admin-views.bulk-purchase-list.list', compact('br','search'));
    }

}
