<?php

namespace App\Http\Controllers\Api\SparePartTransaction\Sell;

use App\Models\CreditPayment;
use App\Models\SellSparepartDetail;
use App\Models\SparePart;
use Illuminate\Http\Request;
use App\Models\SellSparepart;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use PDF;

class SellSparePartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function list()
    {
        try {
            $data = SellSparepart::with('details', 'details.sparepart')->where('status', '!=', 'Closed')->orderBy('created_at', 'desc')->get();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['List Data Sell Sparepart']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function add(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'name'                  => ['required', 'max:255', 'string'],
                'phone'                 => ['nullable', 'max:255', 'string'],
                'address'               => ['nullable', 'max:255', 'string'],
                'payment_method'        => ['required', 'in:Cash,Kredit'],
                'payment_gateway'       => ['required', 'in:Cash,Transfer,QRIS,EDC'],
                'remark'                => ['nullable', 'string', 'max:255'],
                'sparepart'            => ['required', 'array'],
                'sparepart.*.quantity' => ['required'],
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $sell_code = (new \App\Helpers\GlobalGenerateCodeHelper())->generateTransactionCodeSell();

            $data_sell = [
                'transaction_code' => $sell_code,
                'name'             => $request->name,
                'phone'            => $request->phone,
                'address'          => $request->address,
                'total'            => 0,
                'payment_date'     => date('Y-m-d'),
                'payment_method'   => $request->payment_method,
                'payment_gateway'  => $request->payment_gateway,
                'status'           => ($request->payment_method == 'Cash') ? 'Outstanding' : 'Receivable',
                'remark'           => $request->remark,
                'created_by'       => auth()->user()->name,
            ];

            $sell = SellSparepart::create($data_sell);

            if (count($request->sparepart) > 0) {
                $subtotal = 0;
                $sum_subtotal = 0;
                foreach ($request->sparepart as $key => $value) {
                    $spare_part = SparePart::where('id', $value['spare_part_id'])->first();
                    if (!$spare_part) {
                        return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Spare Part Tidak Ditemukan']);
                    }
                    if ($spare_part->stock < $value['quantity']) {
                        return (new \App\Helpers\GlobalResponseHelper())->sendError(['Stock Sparepart Kurang dari Quantity']);
                    }
                    $stock = $spare_part->stock - $value['quantity'];
                    $spare_part->update(['stock' => $stock]);
                    $subtotal = $spare_part->selling_price * $value['quantity'];
                    if ($value['discount']) {
                        $subtotal = $subtotal - $value['discount'];
                    }

                    $data_detail = [
                        'transaction_unique' => $sell->transaction_unique,
                        'spare_part_id'      => $value['spare_part_id'],
                        'quantity'           => $value['quantity'],
                        'discount'           => $value['discount'],
                        'subtotal'           => $subtotal
                    ];
                    SellSparepartDetail::updateOrCreate($data_detail);

                    $sum_subtotal += $subtotal;
                }
                $sell->update([
                    'total' => $sum_subtotal
                ]);
            }

            $data = [
                'transaction_unique' => $sell->transaction_unique,
            ];

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function submitPayment(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'transaction_unique' => ['required']
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $sell = SellSparepart::where('transaction_unique', $request->transaction_unique)->first();
            if (!$sell) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            if ($sell->payment_method == 'Kredit') {
                $validation = Validator::make($request->all(), [
                    'amount' => ['required']
                ]);

                if ($validation->fails()) {
                    return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
                }

                $credit_payment = CreditPayment::where('transaction_unique', $sell->transaction_unique)->get();
                $amount = 0;
                if ($credit_payment->count() > 0) {
                    foreach ($credit_payment as $key => $value) {
                        $amount += $value->amount;
                    }
                }
                $balance = $sell->total - ($amount + (float)$request->amount);

                $credit_payment = CreditPayment::create([
                    'transaction_unique' => $sell->transaction_unique,
                    'date'               => date('Y-m-d'),
                    'total'              => $sell->total,
                    'amount'             => $request->amount,
                    'balance'            => $balance,
                    'created_by'         => auth()->user()->name,
                    'remark'             => $request->remark
                ]);

                if ($balance == 0) {
                    $sell->update([
                        'status'    => 'Closed',
                        'closed_at' => date('Y-m-d'),
                        'closed_by' => auth()->user()->name
                    ]);
                } else {
                    $sell->update([
                        'is_paid' => 1
                    ]);
                }
            } else {
                $sell->update([
                    'status'    => 'Closed',
                    'closed_at' => date('Y-m-d'),
                    'closed_by' => auth()->user()->name,
                    'is_paid'   => 1
                ]);
            }

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Dibayar']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function detail($transaction_unique)
    {
        try {
            $sell = SellSparepart::with('details', 'details.sparepart')->where('transaction_unique', $transaction_unique)->first();

            if (!$sell) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($sell, ['Data Detail Sell']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'transaction_unique'    => ['required'],
                'name'                  => ['required', 'max:255', 'string'],
                'phone'                 => ['nullable', 'max:255', 'string'],
                'address'               => ['nullable', 'max:255', 'string'],
                'payment_method'        => ['required', 'in:Cash,Kredit'],
                'payment_gateway'       => ['required', 'in:Cash,Transfer,QRIS,EDC'],
                'remark'                => ['nullable', 'string', 'max:255'],
                'sparepart'             => ['required', 'array'],
                'sparepart.*.quantity'  => ['required'],
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $sell = SellSparepart::where('transaction_unique', $request->transaction_unique)->first();
            if (!$sell) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data tidak ditemukan']);
            }
            if ($sell->status == 'Closed') {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Tidak dapat melakukan edit,Status Penjualan sudah Closed']);
            }

            $sell->update([
                'name'           => ($request->name) ? $request->name : $sell->name,
                'phone'          => ($request->phone) ? $request->phone : $sell->phone,
                'address'        => ($request->address) ? $request->address : $sell->address,
                'payment_method' => ($request->payment_method) ? $request->payment_method : $sell->payment_method,
                'payment_gateway' => ($request->payment_gateway) ? $request->payment_gateway : $sell->payment_gateway,
                'remark'         => ($request->remark) ? $request->remark : $sell->remark,
                'status'         => ($request->payment_method) ? (($request->payment_method == 'Cash') ? 'Outstanding' : 'Receivable') : $sell->payment_method,
            ]);

            $get_id = [];
            foreach ($request->sparepart as $key => $value) {
                $get_id[] = $value['spare_part_id'];
            };

            $deleteNotInSell = SellSparepartDetail::with('sparepart')->where('transaction_unique', $request->transaction_unique)->whereNotIn('spare_part_id', $get_id)->get();
            if ($deleteNotInSell) {
                foreach ($deleteNotInSell as $keys => $val) {
                    if ($val->sparepart) {
                        $val->sparepart->update([
                            'stock' =>  $val->sparepart->stock + $val->quantity
                        ]);
                    }
                    $val->delete();
                }
            }

            foreach ($request->sparepart as $key => $value) {
                $detail_sell = SellSparepartDetail::where('spare_part_id', $value['spare_part_id'])->where('transaction_unique', $request->transaction_unique)->first();
                $spare_part = Sparepart::where('id', $value['spare_part_id'])->first();
                $subtotal = $spare_part->selling_price * $value['quantity'];
                if ($value['discount']) {
                    $subtotal = $subtotal - $value['discount'];
                }
                if ($detail_sell) {
                    $data_detail = [
                        'spare_part_id'      => ($value['spare_part_id']) ? $value['spare_part_id'] : $detail_sell->spare_part_id,
                        'quantity'           => ($value['quantity']) ? $value['quantity'] : $detail_sell->quantity,
                        'discount'           => ($value['discount']) ? $value['discount'] : $detail_sell->discount,
                        'subtotal'           => $subtotal
                    ];
                    $spare_part->update([
                        'stock' => ($spare_part->stock - $detail_sell->quantity) + $value['quantity']
                    ]);
                    $detail_sell->update($data_detail);
                } else {
                    $data_detail = [
                        'transaction_unique' => $request->transaction_unique,
                        'spare_part_id'      => $value['spare_part_id'],
                        'quantity'           => $value['quantity'],
                        'discount'           => $value['discount'],
                        'subtotal'           => $subtotal
                    ];
                    SellSparepartDetail::create($data_detail);
                    $spare_part->update([
                        'stock' => $spare_part->stock + $value['quantity']
                    ]);
                }
            }
            $sell_detail = SellSparepartDetail::where('transaction_unique', $request->transaction_unique)->get();
            $sum_subtotal = 0;
            if ($sell_detail->count() > 0) {
                foreach ($sell_detail as $val) {
                    $sum_subtotal += $val->subtotal;
                }
            }

            $sell->update([
                'total' => $sum_subtotal
            ]);

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function getInvoice($transaction_unique)
    {
        try {
            $sell = SellSparepart::where('transaction_unique', $transaction_unique)->first();
            if (!$sell) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            $pdf = PDF::loadView('documents.invoice-sell-sparepart')->setPaper('a7');

            return $pdf->stream();
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
