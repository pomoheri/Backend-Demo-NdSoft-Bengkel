<?php

namespace App\Http\Controllers\Api\Laporan;

use App\Models\WorkOrder;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\SellSparepart;
use App\Http\Controllers\Controller;

class LaporanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function getData(Request $request)
    {
        try {
            $month = (isset($request->month) && $request->month) ? $request->month : date('m');
            $year = (isset($request->year) && $request->year) ? $request->year : date('Y');

            $jasa_service = WorkOrder::with('serviceLabour')->where('status', 'Closed')->whereMonth('updated_at',$month)->whereYear('updated_at', $year)->get();
            $sum_jasa_service = 0;
            if($jasa_service->count() > 0){
                foreach ($jasa_service as $value) {
                   if($value->serviceLabour->count() > 0){
                        foreach ($value->serviceLabour as $key => $val) {
                            $sum_jasa_service += $val->subtotal;
                        }
                   }
                }
            }

            $sell_sparepart = SellSparepart::where('status', 'Closed')->whereMonth('closed_at',$month)->whereYear('closed_at', $year)->get()->sum('total');
            $buy_sparepart = PurchaseOrder::where('status', 'Paid')->whereMonth('closed_at',$month)->whereYear('closed_at', $year)->get()->sum('total');

            $output = [
                'jasa_service'   => $sum_jasa_service,
                'sell_sparepart' => $sell_sparepart,
                'buy_sparepart'  => $buy_sparepart
            ];

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($output, ['Data Laporan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
