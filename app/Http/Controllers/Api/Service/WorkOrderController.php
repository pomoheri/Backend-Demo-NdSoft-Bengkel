<?php

namespace App\Http\Controllers\Api\Service;

use App\Models\Labour;
use App\Models\SparePart;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use App\Models\ServiceLabour;
use App\Models\ServiceSublet;
use App\Models\ServiceInvoice;
use App\Models\ServiceRequest;
use App\Models\SellSparepartDetail;
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
            $wo = WorkOrder::with(['vehicle','vehicle.customer','serviceRequest','serviceLabour','serviceLabour.labour','serviceSublet','sellSparepartDetail','sellSparepartDetail.sparepart'])
                            ->where('transaction_unique', $transaction_unique)
                            ->first();
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

            $wo = WorkOrder::with('serviceSublet', 'serviceLabour', 'sellSparepartDetail')->where('transaction_unique', $request->transaction_unique)->first();
            if(!$wo){
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            $wo_code = (new \App\Helpers\GlobalGenerateCodeHelper())->generateTransactionCodeWo();

            //Transaction Service Request
            if($request->service_request && count($request->service_request) > 0){
                $validation_request = Validator::make($request->all(), [
                    'service_request'               => ['required', 'array'],
                    'service_request.*.description' => ['required']
                ]);
                if($validation_request->fails()){
                    return (new \App\Helpers\GlobalResponseHelper())->sendError($validation_request->errors()->all());
                }
                $service_request = $this->serviceRequest($request, $wo);
            }

            //Transaction Service Labour
            if($request->service_labour && count($request->service_labour) > 0){
                $validation_labour = Validator::make($request->all(), [
                    'service_labour'             => ['required', 'array'],
                    'service_labour.*.labour_id' => ['required']
                ]);
                if($validation_labour->fails()){
                    return (new \App\Helpers\GlobalResponseHelper())->sendError($validation_labour->errors()->all());
                }
                $service_labour = $this->serviceLabour($request, $wo);
            }

            //Transaction Sublet
            if ($request->service_sublet && count($request->service_sublet) > 0) {
                $service_sublet = Validator::make($request->all(), [
                    'service_sublet'          => ['required', 'array'],
                    'service_sublet.*.sublet' => ['required'],
                    'service_sublet.*.harga'  => ['required']
                ]);
                if($service_sublet->fails()){
                    return (new \App\Helpers\GlobalResponseHelper())->sendError($service_sublet->errors()->all());
                }
                $service_sublet = $this->serviceSublet($request, $wo);
            }

            //Transaction Sparepart
            if($request->service_part && count($request->service_part) > 0){
                $service_part = Validator::make($request->all(), [
                    'service_part'                 => ['required', 'array'],
                    'service_part.*.spare_part_id' => ['required'],
                    'service_part.*.quantity'      => ['required']
                ]);
                if($service_part->fails()){
                    return (new \App\Helpers\GlobalResponseHelper())->sendError($service_part->errors()->all());
                }
                $service_part = $this->servicePart($request, $wo);
            }

            $wo = WorkOrder::with('serviceSublet', 'serviceLabour', 'sellSparepartDetail')->where('transaction_unique', $request->transaction_unique)->first();
            if(!$wo){
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            $total_sublet = 0;
            if($wo->serviceSublet && count($wo->serviceSublet) > 0){
                foreach ($wo->serviceSublet as $value) {
                    $total_sublet += $value->subtotal;
                }
            }
            $total_labour = 0;
            if($wo->serviceLabour && count($wo->serviceLabour) > 0){
                foreach ($wo->serviceLabour as $val) {
                    $total_labour += $val->subtotal;
                }
            }
            $total_part = 0;
            if($wo->sellSparepartDetail && count($wo->sellSparepartDetail) > 0){
                foreach ($wo->sellSparepartDetail as $part) {
                    $total_part += $part->subtotal;
                }
            }

            $data = [
                'transaction_code' => $wo_code,
                'status'           => 'New',
                'total'            => $total_sublet + $total_labour + $total_part,
                'remark'           => $request->remark,
                'updated_by'       => auth()->user()->name,
                'technician'       => $request->technician
            ];
            
            $wo->update($data);

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($wo, ['Data Berhasil Disimpan']);

        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    protected function serviceRequest(Request $request, $wo)
    {
        $get_id = [];
        foreach ($request->service_request as $key => $value) {
            $get_id[] = $value['id'];
        };

        $deleteNotrequest = ServiceRequest::where('transaction_unique', $request->transaction_unique)->whereNotIn('id', $get_id)->delete();
        foreach ($request->service_request as $key => $value) {
            $detail_request = ServiceRequest::where('id', $value['id'])->where('transaction_unique', $request->transaction_unique)->first();
            $data_detail = [
                'transaction_unique' => $request->transaction_unique,
                'request'            => $value['description']
            ];
            if ($detail_request) {
                $detail_request->update($data_detail);
            } else {
                ServiceRequest::create($data_detail);
            }
        }
    }

    protected function serviceLabour(Request $request, $wo)
    {
        $get_id = [];
        foreach ($request->service_labour as $key => $value) {
            $get_id[] = $value['id'];
        };

        $deleteNotrequest = ServiceLabour::where('transaction_unique', $request->transaction_unique)->whereNotIn('id', $get_id)->delete();
        foreach ($request->service_labour as $key => $value) {
            $detail_labour = ServiceLabour::where('id', $value['id'])->where('transaction_unique', $request->transaction_unique)->first();
            $labour = Labour::where('id', $value['labour_id'])->first();
            $subtotal = ($labour) ? ($labour->price * $value['frt']) - $value['discount'] : 0;
            $data_detail = [
                'transaction_unique' => $request->transaction_unique,
                'labour_id'          => $value['labour_id'],
                'frt'                => $value['frt'],
                'discount'           => $value['discount'],
                'subtotal'           => $subtotal
            ];
            if ($detail_labour) {
                $detail_labour->update($data_detail);
            } else {
                ServiceLabour::create($data_detail);
            }
        }
    }

    protected function serviceSublet(Request $request, $wo)
    {
        $get_id = [];
        foreach ($request->service_sublet as $key => $value) {
            $get_id[] = $value['id'];
        };

        $deleteNotrequest = ServiceSublet::where('transaction_unique', $request->transaction_unique)->whereNotIn('id', $get_id)->delete();
        foreach ($request->service_sublet as $key => $value) {
            $detail_sublet = ServiceSublet::where('id', $value['id'])->where('transaction_unique', $request->transaction_unique)->first();
            $data_detail = [
                'transaction_unique' => $request->transaction_unique,
                'sublet'             => $value['sublet'],
                'subtotal'           => $value['harga']
            ];
            if ($detail_sublet) {
                $detail_sublet->update($data_detail);
            } else {
                ServiceSublet::create($data_detail);
            }
        }
    }

    protected function servicePart(Request $request, $wo)
    {
        $get_id = [];
        foreach ($request->service_part as $key => $value) {
            $get_id[] = $value['id'];
        };

        $deleteNotrequest = SellSparepartDetail::where('transaction_unique', $request->transaction_unique)->whereNotIn('id', $get_id)->delete();
        foreach ($request->service_part as $key => $value) {
            $detail_part = SellSparepartDetail::where('transaction_unique', $request->transaction_unique)->where('id', $value['id'])->first();
            $sparepart = SparePart::where('id', $value['spare_part_id'])->first();
            $subtotal = ($sparepart) ? ($sparepart->selling_price * $value['quantity']) - $value['discount'] : 0;
            $data_detail = [
                'transaction_unique' => $request->transaction_unique,
                'spare_part_id'      => $value['spare_part_id'],
                'quantity'           => $value['quantity'],
                'discount'           => $value['discount'],
                'subtotal'           => $subtotal
            ];
            if ($detail_part) {
                $detail_part->update($data_detail);
                $stock = ($sparepart->stock + $detail_part->quantity) - $value['quantity'];
            } else {
                SellSparepartDetail::create($data_detail);
                $stock = $sparepart->stock - $value['quantity'];
            }
            $sparepart->update([
                'stock' => $stock
            ]);
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'transaction_unique' => ['required'],
                'status'             => ['required', 'in:On Progress,Outstanding'],
            ]);

            if($validation->fails()){
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $wo = WorkOrder::where('transaction_unique', $request->transaction_unique)->first();
            if(!$wo){
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }
            if($request->status == 'Outstanding'){
                $invoice_code = (new \App\Helpers\GlobalGenerateCodeHelper())->generateTransactionCodeInvoice();

                $data_invoice = [
                    'transaction_code'    => $invoice_code,
                    'transaction_unique'  => $wo->transaction_unique,
                    'status'              => 'Outstanding',
                    'created_by'          => auth()->user()->name
                ];
                $invoice = ServiceInvoice::create($data_invoice);
            }

            $wo->update([
                'status' => $request->status
            ]);

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Disimpan']);

        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
