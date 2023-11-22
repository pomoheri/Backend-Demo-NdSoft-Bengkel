<?php

namespace App\Http\Controllers\Api\Service;

use App\Models\CreditPayment;
use Illuminate\Http\Request;
use App\Models\ServiceInvoice;
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
            if (isset($request->start_date) && $request->start_date) {
                $data = $data->where('created_at', '>=', $request->start_date);
            }
            if (isset($request->end_date) && $request->end_date) {
                $data = $data->where('created_at', '<=', $request->end_date);
            }
            $data = $data->where('status', '!=', 'Closed')->with('workOrder', 'workOrder.vehicle', 'workOrder.vehicle.carType', 'workOrder.vehicle.carType.carBrand', 'workOrder.vehicle.customer')->orderBy('created_at', 'desc')->get();

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['List Data HandOver']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function detail($transaction_unique)
    {
        try {
            $invoice = ServiceInvoice::with('workOrder', 'workOrder.vehicle', 'workOrder.vehicle.customer', 'workOrder.serviceRequest', 'workOrder.serviceLabour', 'workOrder.serviceSublet', 'workOrder.sellSparepartDetail')
                ->where('transaction_unique', $transaction_unique)
                ->first();
            if (!$invoice) {
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

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $invoice = ServiceInvoice::with('workOrder')->where('transaction_unique', $request->transaction_unique)->first();
            if (!$invoice) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            if ($request->payment_method == 'Tunai') {
                $invoice->update([
                    'payment_method'  => $request->payment_method,
                    'payment_gateway' => $request->payment_gateway,
                    'status'          => 'Closed',
                    'closed_by'       => auth()->user()->name,
                    'closed_at'       => date('Y-m-d')
                ]);
                if ($invoice->workOrder) {
                    $invoice->workOrder->update([
                        'status' => 'Closed'
                    ]);
                }
            } else {
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
                    return (new \App\Helpers\GlobalResponseHelper())->sendResponse($balance, ['Berhasil Melakukan Pembayaran']);
                } else {
                    $invoice->update([
                        'status'          => 'Closed',
                        'closed_by'       => auth()->user()->name,
                        'closed_at'       => date('Y-m-d')
                    ]);
                    if ($invoice->workOrder) {
                        $invoice->workOrder->update([
                            'status' => 'Closed'
                        ]);
                    }
                }
            }

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
