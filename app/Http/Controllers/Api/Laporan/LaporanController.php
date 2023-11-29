<?php

namespace App\Http\Controllers\Api\Laporan;

use App\Models\SmallTransaction;
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

            $jasa_service = WorkOrder::with('serviceLabour','serviceSublet','sellSparepartDetail','sellSparepartDetail.sparepart')->where('status', 'Closed')->whereMonth('updated_at',$month)->whereYear('updated_at', $year)->get();
            $sum_jasa_service = 0;
            $sum_sublet = 0;
            $sum_part = 0;
            $profit_part = 0;
            if($jasa_service->count() > 0){
                foreach ($jasa_service as $value) {
                   if($value->serviceLabour->count() > 0){
                        foreach ($value->serviceLabour as $key => $val) {
                            $sum_jasa_service += $val->subtotal;
                        }
                   }
                   if($value->serviceSublet->count() > 0){
                        foreach($value->serviceSublet as $sublet){
                            $sum_sublet += $sublet->subtotal;
                        }
                   }
                   if($value->sellSparepartDetail->count() > 0) {
                        foreach ($value->sellSparepartDetail as $key => $part) {
                           $sum_part += $part->subtotal;
                           $profit_part += ($part->sparepart) ? $part->sparepart->profit : 0;
                        }
                   }
                }
            }

            $query_sell_part = SellSparepart::with('details','details.sparepart')->where('status', 'Closed')->whereMonth('closed_at',$month)->whereYear('closed_at', $year)->get();
            $sell_sparepart = 0;
            $profit_sell_part = 0;
            if($query_sell_part){
                foreach ($query_sell_part as $key => $sell_part) {
                    $sell_sparepart += $sell_part->total;
                    if($sell_part->count() > 0){
                        foreach($sell_part->details as $det_part){
                            $profit_sell_part += ($det_part->sparepart) ? $det_part->sparepart->profit : 0;
                        }
                    }
                }
            }


            $buy_sparepart = PurchaseOrder::where('status', 'Paid')->whereMonth('closed_at',$month)->whereYear('closed_at', $year)->get()->sum('total');
            $total_cost = SmallTransaction::where('status', 'Kredit')->whereMonth('created_at',$month)->whereYear('created_at', $year)->where('category','Cost')->get()->sum('total');

            $output = [
                'jasa_service'   => $sum_jasa_service,
                'total_sublet'   => $sum_sublet,
                'sell_sparepart' => $sell_sparepart+$sum_part,
                'buy_sparepart'  => $buy_sparepart,
                'total_cost'     => $total_cost,
                'profit_part'    => 'Profit Nilai nya masih belum fix, bimbang'
            ];

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($output, ['Data Laporan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
