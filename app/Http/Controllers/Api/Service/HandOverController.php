<?php

namespace App\Http\Controllers\Api\Service;

use App\Models\Estimation;
use App\Models\EstimationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use PDF;

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
            $data = $data->with('Vehicle')->orderBy('created_at', 'desc')->get();
            
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
           ]);

           if($validation->fails()){
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
           }

           $data = [
                'vehicle_id' => $request->vehicle_id,
                'status'     => 'Draft',
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

            $pdf_url = asset($directory);

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($pdf_url,['Data Berhasil Di Generate']);

        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
