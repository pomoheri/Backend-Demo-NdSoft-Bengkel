<?php

namespace App\Http\Controllers\Api\Service;

use App\Http\Controllers\Controller;
use App\Models\ServiceInvoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function list(Request $request)
    {
        try {
            $data = ServiceInvoice::query();
            if(isset($request->start_date) && $request->start_date){
                $data = $data->where('created_at', '>=' ,$request->start_date);
            }
            if(isset($request->end_date) && $request->end_date){
                $data = $data->where('created_at', '<=' ,$request->end_date);
            }
            $data = $data->where('status', '!=', 'Closed')->with('workOrder','workOrder.vehicle','workOrder.vehicle.customer')->orderBy('created_at', 'desc')->get();
            
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['List Data HandOver']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function detail($transaction_unique)
    {
        try {
            $invoice = ServiceInvoice::with('workOrder','workOrder.vehicle','workOrder.vehicle.customer', 'workOrder.serviceRequest' , 'workOrder.serviceLabour', 'workOrder.serviceSublet', 'workOrder.sellSparepartDetail')
                                    ->where('transaction_unique', $transaction_unique)
                                    ->first();
            if(!$invoice) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($invoice, ['Data Detail Invoice']);

        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
