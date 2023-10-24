<?php

namespace App\Http\Controllers\Api\SparePartTransaction\Purchasing;

use Illuminate\Http\Request;
use App\Models\CreditPayment;
use App\Http\Controllers\Controller;

class KreditHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function kreditHistory($transaction_unique)
    {
        try {
            $data = CreditPayment::select('date','total as total_payment','amount','balance','remark')
                                ->where('transaction_unique', $transaction_unique)
                                ->orderBy('created_at', 'asc')
                                ->get();

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['List Data Kredit History']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
