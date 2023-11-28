<?php

namespace App\Http\Controllers\Api\Service;

use App\Models\Labour;
use App\Models\SparePart;
use App\Models\Estimation;
use Illuminate\Http\Request;
use App\Models\EstimationLabour;
use App\Models\EstimationSublet;
use App\Models\EstimationRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class EstimationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function list(Request $request)
    {
        try {
            $estimation = Estimation::query();
            if(isset($request->start_date) && $request->start_date){
                $estimation = $estimation->where('created_at', '>=', $request->start_date);
            }
            if(isset($request->end_date) && $request->end_date){
                $estimation = $estimation->where('created_at', '<=', $request->end_date);
            }

            $estimation = $estimation->join('Vehicle', 'estimationRequest')->whereIn('status', ['Darft','New'])->orderBy('created_at', 'desc')->get();

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($estimation, ['Data List Estimation']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function add(Request $request)
    {
        try {
            $validation = Validator::make($request->all(),[
                'vehicle_id' => ['required', 'integer']
            ]);

            if(!$validation->fails()){
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $data = [
                'vehicle_id' => $request->vehicle_id,
                'remark'     => $request->remark,
                'status'     => 'Draft',
                'created_by' => auth()->user()->name
            ];

            $estimation = Estimation::create($data);

            if ($request->estimation_request && count($request->estimation_request) > 0) {
                foreach ($request->estimation_request as $key => $value) {
                    $data_request = [
                        'estimation_unique' => $estimation->estimation_unique,
                        'request'           => $value['description'],
                    ];
                    EstimationRequest::updateOrCreate($data_request);
                }
            }

            if ($request->estimation_labour && count($request->estimation_labour) > 0) {
                foreach ($request->estimation_labour as $key => $value) {
                    $labour = Labour::where('id', $value['labour_id'])->first();
                    $subotalbeforediskon = ($labour) ? ($labour->price * $value['frt']) : 0;
                    $diskon = ($subotalbeforediskon*$value['discount'])/100;
                    $subtotal = $subotalbeforediskon - $diskon;
                    $data_detail = [
                        'estimation_unique'  => $request->estimation_unique,
                        'labour_id'          => $value['labour_id'],
                        'frt'                => $value['frt'],
                        'discount'           => $value['discount'],
                        'subtotal'           => $subtotal
                    ];
                    EstimationLabour::create($data_detail);
                }
            }

            if($request->estimation_sublet && count($request->estimation_sublet) > 0){
                foreach ($request->estimation_sublet as $key => $value) {
                    $data_detail_sublet = [
                        'estimation_unique'  => $request->estimation_unique,
                        'sublet'             => $value['sublet'],
                        'subtotal'           => $value['harga']
                    ];
                    EstimationSublet::create($data_detail_sublet);
                }
            }

            if($request->estimation_part && count($request->estimation_part) > 0){
                foreach ($request->estimation_part as $key => $value) {
                    $sparepart = SparePart::where('id', $value['spare_part_id'])->first();
                    $subotalbeforediskon = ($sparepart) ? ($sparepart->selling_price * $value['quantity']) : 0;
                    $diskon = ($subotalbeforediskon*$value['discount'])/100;
                    $subtotal = $subotalbeforediskon - $diskon;
                    $data_detail_part = [
                        'estimation_unique' => $request->estimation_unique,
                        'sparepart_id'      => $sparepart->id,
                        'quantity'          => $value['quantity'],
                        'subtotal'          => $subtotal,
                        'discount'          => $value['discount'],
                        'total'             => $subtotal - $diskon,
                        'profit'            => 0
                    ];
                }
            }
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
