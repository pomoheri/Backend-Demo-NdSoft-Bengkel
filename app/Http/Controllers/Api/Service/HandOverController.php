<?php

namespace App\Http\Controllers\Api\Service;

use App\Models\ServiceRequest;
use PDF;
use App\Models\WorkOrder;
use App\Models\Estimation;
use Illuminate\Http\Request;
use App\Models\EstimationRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class HandOverController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function list(Request $request)
    {
        try {
            $data = Estimation::query();
            if(isset($request->start_date) && $request->start_date){
                $data = $data->where('created_at', '>=' ,$request->start_date);
            }
            if(isset($request->end_date) && $request->end_date){
                $data = $data->where('created_at', '<=' ,$request->end_date);
            }
            $data = $data->where('status', 'Draft')->with('Vehicle')->orderBy('created_at', 'desc')->get();
            
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['List Data HandOver']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function add(Request $request)
    {
        try {
           $validation = Validator::make($request->all(),[
                'vehicle_id'            => ['required', 'integer'],
                'request_order'               => ['required', 'array'],
                'request_order.*.description' => ['required'],
                'carrier'               => ['required'],
                'carrier_phone'         => ['required']
           ]);

           if($validation->fails()){
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
           }

           $data = [
                'vehicle_id' => $request->vehicle_id,
                'status'     => 'Draft',
                'carrier'    => $request->carrier,
                'carrier_phone'    => $request->carrier_phone,
                'created_by' => auth()->user()->name
           ];

           $estimation = Estimation::create($data);

           if (count($request->request_order) > 0) {
                foreach ($request->request_order as $key => $value) {
                    $data_request = [
                        'estimation_unique' => $estimation->estimation_unique,
                        'request'           => $value['description'],
                    ];
                    EstimationRequest::updateOrCreate($data_request);
                }
           }

           return (new \App\Helpers\GlobalResponseHelper())->sendResponse($estimation, ['Data Berhasil Disimpan']);

        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function detail($estimation_unique){
        try {
            $estimation = Estimation::with('Vehicle','Vehicle.customer','Vehicle.carType','Vehicle.carType.carBrand','estimationRequest')->where('estimation_unique', $estimation_unique)->first();
            if(!$estimation) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($estimation, ['Data Detail']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $validation = Validator::make($request->all(),[
                'estimation_unique'           => ['required'],
                'vehicle_id'                  => ['required', 'integer'],
                'request_order'               => ['required', 'array'],
                'request_order.*.description' => ['required'],
                'carrier'                     => ['required', 'max:200'],
                'carrier_phone'               => ['required', 'max:15']
           ]);

           if($validation->fails()){
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

           $estimation = Estimation::where('estimation_unique', $request->estimation_unique)->first();
           if(!$estimation){
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
           }
           if($estimation->status == 'New' || $estimation->status == 'Transfered'){
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Gagal, Data sudah di keluarkan']);
           }

           $data = [
                'vehicle_id'    => $request->vehicle_id,
                'updated_by'    => auth()->user()->name,
                'carrier'       => $request->carrier,
                'carrier_phone' => $request->carrier_phone,
            ];

            $estimation->update($data);

            $get_id = [];
            foreach ($request->request_order as $key => $value) {
                $get_id[] = $value['id'];
            };

            $deleteNotrequest = EstimationRequest::where('estimation_unique', $request->estimation_unique)->whereNotIn('id', $get_id)->delete();

            foreach ($request->request_order as $key => $value) {
                $detail_request = EstimationRequest::where('id', $value['id'])->where('estimation_unique', $request->estimation_unique)->first();
               
                if ($detail_request) {
                    $data_detail = [
                        'request'      => $value['description']
                    ];
                    $detail_request->update($data_detail);
                } else {
                    $data_detail = [
                        'estimation_unique'  => $request->estimation_unique,
                        'request'            => $value['description']
                    ];
                    EstimationRequest::create($data_detail);
                }
            }

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($estimation, ['Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function printHandOver($estimation_unique)
    {
        try {
            $estimation = Estimation::with('Vehicle','Vehicle.customer','Vehicle.carType','Vehicle.carType.carBrand','estimationRequest')->where('estimation_unique', $estimation_unique)->first();
            if(!$estimation) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            $data = [
                'estimation' => $estimation
            ];
            $pdf = PDF::loadView('documents.hand-over-document', $data)->setPaper('a4', 'potrait');

            $pdf_file = $pdf->output();
            
            $directory = 'public/handover/'.$estimation_unique.'.pdf';

            \Storage::put($directory,$pdf_file);

            $pdf_url = env('APP_URL').\Storage::url($directory);

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($pdf_url,['Data Berhasil Di Generate']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function delete($estimation_unique)
    {
        try {
            $estimation = Estimation::where('estimation_unique',$estimation_unique)->first();
            if(!$estimation){
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }
            if($estimation->status == 'New' || $estimation->status == 'Transfered'){
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Gagal, Data sudah di keluarkan']);
           }
            $estimation->delete();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([],['Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function transferToWo($estimation_unique)
    {
        try {
            $estimation = Estimation::with('Vehicle','estimationRequest')->where('estimation_unique', $estimation_unique)->first();
            if(!$estimation){
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            $work_order = WorkOrder::where('transaction_unique', $estimation->estimation_unique)->first();
            if($work_order){
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Sudah ada di Work Order']);
            }else{
                $wo_code = (new \App\Helpers\GlobalGenerateCodeHelper())->generateTransactionCodeWo();
                
                $work_order = WorkOrder::create([
                    'transaction_code'     => $wo_code,
                    'transaction_unique'   => $estimation->estimation_unique,
                    'vehicle_id'           => $estimation->vehicle_id,
                    'total'                => 0,
                    'status'               => 'Draft',
                    'carrier'              => $estimation->carrier,
                    'carrier_phone'        => $estimation->carrier_phone,
                    'created_by'           => auth()->user()->name
                ]);

                if($estimation->estimationRequest){
                    foreach ($estimation->estimationRequest as $key => $value) {
                        $service_request = ServiceRequest::create([
                            'transaction_unique' => $work_order->transaction_unique,
                            'request'            => $value->request
                        ]);
                    }
                }

                $estimation->update([
                    'status' => 'Transfered'
                ]);
            }
            
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([$work_order],['Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
