<?php

namespace App\Http\Controllers\Api\Service;

use App\Models\Labour;
use App\Models\ServiceLabour;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class WorkOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function list(Request $request)
    {
        try {
            $data = WorkOrder::query();
            if(isset($request->start_date) && $request->start_date){
                $data = $data->where('created_at', '>=', $request->start_date);
            }
            if(isset($request->end_date) && $request->end_date){
                $data = $data->where('created_at', '<=', $request->end_date);
            }

            $data = $data->where('status', '!=', 'Closed')->orderBy('created_at', 'desc')->get();

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['List Data Work order']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function detail($transaction_unique)
    {
        try {
            $wo = WorkOrder::with(['vehicle','vehicle.customer','serviceRequest'])->where('transaction_unique', $transaction_unique)->first();
            if(!$wo){
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($wo, ['Detail Work Order']);
            
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function updateWo(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'transaction_unique'     => ['required']
            ]);

            if($validation->fails()){
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $wo = WorkOrder::where('transaction_unique', $request->transaction_unique)->first();
            if(!$wo){
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            $data = [
                'remark'     => $request->remark,
                'updated_by' => auth()->user()->name,
                'technician' => $request->technician
            ];

            $wo->update($data);

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($wo, ['Data Berhasil Disimpan']);

        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
