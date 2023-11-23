<?php

namespace App\Http\Controllers\Api\Service;

use PDF;
use App\Models\Labour;
use App\Models\SparePart;
use Illuminate\Http\Request;
use App\Models\CreditPayment;
use App\Models\ServiceLabour;
use App\Models\ServiceSublet;
use App\Models\ServiceInvoice;
use App\Models\ServiceRequest;
use App\Models\SellSparepartDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

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
            
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['List Data Invoice']);
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

    public function submitPayment(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'transaction_unique' => ['required'],
                'payment_method'     => ['required'],
                'payment_gateway'    => ['required']
            ]);

            if($validation->fails()){
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $invoice = ServiceInvoice::with('workOrder')->where('transaction_unique', $request->transaction_unique)->first();
            if(!$invoice){
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            if($request->payment_method == 'Tunai'){
                $invoice->update([
                    'payment_method'  => $request->payment_method,
                    'payment_gateway' => $request->payment_gateway,
                    'status'          => 'Closed',
                    'closed_by'       => auth()->user()->name,
                    'closed_at'       => date('Y-m-d')
                ]);
                if($invoice->workOrder){
                    $invoice->workOrder->update([
                        'status' => 'Closed'
                    ]);
                }
            }else{
                $credit_payment = CreditPayment::where('transaction_unique', $invoice->transaction_unique)->get();
                $amount = 0;
                if ($credit_payment->count() > 0) {
                    foreach ($credit_payment as $key => $value) {
                        $amount += $value->amount;
                    }
                }
                $balance = ($invoice->workOrder) ? $invoice->workOrder->total - ($amount + (float)$request->amount) : 0;
                $create_credit_payment = CreditPayment::create([
                    'transaction_unique' => $invoice->transaction_unique,
                    'date'               => date('Y-m-d'),
                    'total'              => ($invoice->workOrder) ? $invoice->workOrder->total : 0,
                    'amount'             => $request->amount,
                    'balance'            => $balance,
                    'created_by'         => auth()->user()->name,
                    'remark'             => $request->remark
                ]);

                if ($balance != 0) {
                    $invoice->update([
                        'payment_method'  => $request->payment_method,
                        'payment_gateway' => $request->payment_gateway,
                        'is_paid'         => 1,
                        'status'          => 'Not Paid'
                    ]);
                    return (new \App\Helpers\GlobalResponseHelper())->sendResponse($balance,['Berhasil Melakukan Pembayaran']);
                }else{
                    $invoice->update([
                        'status'          => 'Closed',
                        'closed_by'       => auth()->user()->name,
                        'closed_at'       => date('Y-m-d')
                    ]);
                    if($invoice->workOrder){
                        $invoice->workOrder->update([
                            'status' => 'Closed'
                        ]);
                    }
                }
            }

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([],['Data Berhasil Disimpan']);
        } catch (\Exception $e) {
           return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function getPdf($transaction_unique)
    {
        try {
            $invoice = ServiceInvoice::with('workOrder', 'workOrder.vehicle' ,'workOrder.vehicle.customer')->where('transaction_unique', $transaction_unique)->first();
            if(!$invoice){
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            $data = [
                'invoice' => $invoice,
                'workOrder' => $invoice->workOrder,
                'vehicle' => ($invoice->workOrder) ? $invoice->workOrder->vehicle : null,
                'customer' => ($invoice->workOrder) ? (($invoice->workOrder->vehicle) ? $invoice->workOrder->vehicle->customer : null) : null
            ];

            $pdf = PDF::loadView('documents.service-invoice', $data)->setPaper('a4', 'potrait');

            $pdf_file = $pdf->stream();

            return $pdf_file;

        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
         }
    }

    public function updateInvoice(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'transaction_unique' => ['required'],
                'payment_method'     => ['required'],
                'payment_gateway'    => ['required'],

            ]);

            if($validation->fails()){
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $invoice = ServiceInvoice::with('workOrder','workOrder.serviceRequest', 'workOrder.serviceSublet', 'workOrder.serviceLabour', 'workOrder.sellSparepartDetail')
                                ->where('transaction_unique', $request->transaction_unique)
                                ->first();
            if(!$invoice){
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            //Update Service Request
            if($request->service_request && count($request->service_request) > 0){
                $validation_request = Validator::make($request->all(), [
                    'service_request'               => ['required', 'array'],
                    'service_request.*.id'          => ['required'],
                    'service_request.*.solution'    => ['required']
                ]);
                if($validation_request->fails()){
                    return (new \App\Helpers\GlobalResponseHelper())->sendError($validation_request->errors()->all());
                }
                
                foreach ($request->service_request as $key => $value) {
                    $detail_request = ServiceRequest::where('id', $value['id'])->where('transaction_unique', $request->transaction_unique)->first();
                    $data_detail = [
                        'solution'           => $value['solution']
                    ];
                    if ($detail_request) {
                        $detail_request->update($data_detail);
                    }
                }
            }

            //Update Service Labour
            if($request->service_labour && count($request->service_labour) > 0){
                $validation_labour = Validator::make($request->all(), [
                    'service_labour'             => ['required', 'array'],
                    'service_labour.*.id'        => ['required'],
                    'service_labour.*.frt'       => ['required']
                ]);
                if($validation_labour->fails()){
                    return (new \App\Helpers\GlobalResponseHelper())->sendError($validation_labour->errors()->all());
                }
                
                foreach ($request->service_labour as $key => $value) {
                    $detail_labour = ServiceLabour::where('id', $value['id'])->where('transaction_unique', $request->transaction_unique)->first();
                    $labour = Labour::where('id', $detail_labour->labour_id)->first();
                    $subtotal = ($labour) ? ($labour->price * $value['frt']) - $value['discount'] : 0;
                    $data_detail = [
                        'frt'                => $value['frt'],
                        'discount'           => $value['discount'],
                        'subtotal'           => $subtotal
                    ];
                    if ($detail_labour) {
                        $detail_labour->update($data_detail);
                    }
                }
            }

            //Update Sublet
            if ($request->service_sublet && count($request->service_sublet) > 0) {
                $service_sublet = Validator::make($request->all(), [
                    'service_sublet'          => ['required', 'array'],
                    'service_sublet.*.id'     => ['required'],
                    'service_sublet.*.sublet' => ['required'],
                    'service_sublet.*.harga'  => ['required']
                ]);
                if($service_sublet->fails()){
                    return (new \App\Helpers\GlobalResponseHelper())->sendError($service_sublet->errors()->all());
                }

                foreach ($request->service_sublet as $key => $value) {
                    $detail_sublet = ServiceSublet::where('id', $value['id'])->where('transaction_unique', $request->transaction_unique)->first();
                    $data_detail = [
                        'sublet'             => $value['sublet'],
                        'subtotal'           => $value['harga']
                    ];
                    if ($detail_sublet) {
                        $detail_sublet->update($data_detail);
                    } 
                }
            }

            //Update Sparepart
            if($request->service_part && count($request->service_part) > 0){
                $service_part = Validator::make($request->all(), [
                    'service_part'                 => ['required', 'array'],
                    'service_part.*.id'            => ['required'],
                    'service_part.*.quantity'      => ['required']
                ]);
                if($service_part->fails()){
                    return (new \App\Helpers\GlobalResponseHelper())->sendError($service_part->errors()->all());
                }

                foreach ($request->service_part as $key => $value) {
                    $detail_part = SellSparepartDetail::where('transaction_unique', $request->transaction_unique)->where('id', $value['id'])->first();
                    $sparepart = SparePart::where('id', $detail_part->spare_part_id)->first();
                    $subtotal = ($sparepart) ? ($sparepart->selling_price * $value['quantity']) - $value['discount'] : 0;
                    $data_detail = [
                        'quantity'           => $value['quantity'],
                        'discount'           => $value['discount'],
                        'subtotal'           => $subtotal
                    ];
                    if ($detail_part) {
                        $detail_part->update($data_detail);
                        $stock = ($sparepart->stock + $detail_part->quantity) - $value['quantity'];
                        $sparepart->update([
                            'stock' => $stock
                        ]);
                    }
                }
            }

            if($invoice->workOrder){
                $total_sublet = 0;
                if($invoice->workOrder->serviceSublet && count($invoice->workOrder->serviceSublet) > 0){
                    foreach ($invoice->workOrder->serviceSublet as $value) {
                        $total_sublet += $value->subtotal;
                    }
                }
                $total_labour = 0;
                if($invoice->workOrder->serviceLabour && count($invoice->workOrder->serviceLabour) > 0){
                    foreach ($invoice->workOrder->serviceLabour as $val) {
                        $total_labour += $val->subtotal;
                    }
                }
                $total_part = 0;
                if($invoice->workOrder->sellSparepartDetail && count($invoice->workOrder->sellSparepartDetail) > 0){
                    foreach ($invoice->workOrder->sellSparepartDetail as $part) {
                        $total_part += $part->subtotal;
                    }
                }
                $data = [
                    'total'            => $total_sublet + $total_labour + $total_part,
                    'remark'           => $request->remark,
                    'updated_by'       => auth()->user()->name
                ];
                $invoice->update($data);
            }

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($invoice, ['Data Berhasil Di Update']);
            
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
         }
    }
}
