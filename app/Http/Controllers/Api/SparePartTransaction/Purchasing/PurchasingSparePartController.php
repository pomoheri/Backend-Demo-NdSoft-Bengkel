<?php

namespace App\Http\Controllers\Api\SparePartTransaction\Purchasing;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use App\Models\Supplier;
use App\Models\SparePart;
use Illuminate\Http\Request;
use App\Models\CreditPayment;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PurchasingSparePartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function list()
    {
        try {
            $data = PurchaseOrder::with('suppliers', 'details')->orderBy('created_at', 'desc')->get();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['List Data Purchase Order']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function add(Request $request)
    {
        try {
            $validation = $this->validatePurchaseOrderRequest($request);
            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            if ($request->supplier_id) {
                $supplier = Supplier::where('id', $request->supplier_id)->first();
            } else {
                $validation = Validator::make($request->all(), [
                    'name'      => ['required', 'string', 'max:200', 'unique:supplier,name'],
                    'address'   => ['required', 'string', 'max:255'],
                    'pic'       => ['required', 'max:100'],
                    'phone'     => ['nullable', 'string', 'max:18']
                ]);

                if ($validation->fails()) {
                    return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
                }

                $supplier = $this->getOrCreateSupplier($request);
            }

            $purchase_order = $this->createOrUpdatePurchaseOrder($request, $supplier);

            $sum_subtotal = 0;
            if (count($request->spare_part) > 0) {
                foreach ($request->spare_part as $key => $value) {
                    if (!$value['spare_part_id']) {
                        $validation = Validator::make($value, [
                            'name'          => ['required', 'string', 'max:255', 'unique:spare_part,name'],
                            'car_brand_id'  => ['required'],
                            'category'      => ['required', 'in:Spare Part, Material, Asset'],
                            'selling_price' => ['required'],
                            'part_number'   => ['required', 'string', 'max:255', 'unique:spare_part,part_number'],
                        ]);

                        if ($validation->fails()) {
                            return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
                        }

                        $data_spare_part = [
                            'part_number'   => $value['part_number'],
                            'name'          => $value['name'],
                            'car_brand_id'  => $value['car_brand_id'],
                            'is_genuine'    => $value['is_genuine'],
                            'category'      => $value['category'],
                            'selling_price' => $value['selling_price'],
                            'location_id'   => $value['location_id'],
                            'created_by'    => auth()->user()->name
                        ];

                        $spare_part = SparePart::updateOrCreate($data_spare_part);
                    }
                    $data_detail = [
                        'transaction_unique' => $purchase_order->transaction_unique,
                        'spare_part_id'      => ($value['spare_part_id']) ? $value['spare_part_id'] : $spare_part->id,
                        'quantity'           => $value['quantity'],
                        'perpiece'           => $value['per_piece'],
                        'subtotal'           => ($value['quantity'] != 0) ? $value['quantity'] * $value['per_piece'] : 0
                    ];
                    PurchaseOrderDetail::updateOrCreate($data_detail);
                    $sum_subtotal += ($value['quantity'] != 0) ? $value['quantity'] * $value['per_piece'] : 0;
                }
            }
            $purchase_order->update([
                'total' => $sum_subtotal
            ]);

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    private function validatePurchaseOrderRequest(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'invoice_number'        => ['required'],
            'invoice_date'          => ['required', 'date'],
            'payment_method'        => ['required', 'in:Cash,Kredit'],
            'spare_part'            => ['required', 'array'],
            'spare_part.*.quantity' => ['required'],
            'spare_part.*.per_piece' => ['required'],
            'remark'                 => ['nullable', 'string', 'max:255'],

            'spare_part.*.name'          => ['nullable', 'string', 'max:255'],
            'spare_part.*.car_brand_id'  => ['nullable'],
            'spare_part.*.category'      => ['nullable', 'in:Spare Part, Material, Asset'],
            'spare_part.*.selling_price' => ['nullable'],
            'spare_part.*.part_number'   => ['nullable', 'string', 'max:255'],
        ]);

        return $validation;
    }
    private function getOrCreateSupplier(Request $request)
    {
        $supplier_code = (new \App\Helpers\GlobalGenerateCodeHelper())->generateSupplierCode();

        $send_supplier = [
            'supplier_code' => $supplier_code,
            'name'          => $request->name,
            'address'       => $request->address,
            'pic'           => $request->pic,
            'phone'         => $request->phone,
            'created_by'    => auth()->user()->name
        ];

        return Supplier::updateOrCreate($send_supplier);
    }
    private function createOrUpdatePurchaseOrder(Request $request, $supplier)
    {
        $payment_due_date = null;
        if (strtolower($request->payment_method) == 'kredit') {
            $invoice_date = Carbon::parse($request->invoice_date);
            $payment_due_date = $invoice_date->addDays($request->payment_due);
        }
        return PurchaseOrder::updateOrCreate([
            'supplier_id'    => $supplier->id,
            'invoice_number' => $request->invoice_number,
            'invoice_date'   => $request->invoice_date,
        ], [
            'status'         => 'Draft',
            'created_by'     => auth()->user()->name,
            'payment_method' => $request->payment_method,
            'remark'         => $request->remark,
            // 'payment_due_date' => $payment_due_date
            'payment_due_date' => $request->payment_due_date
        ]);
    }
    public function terimaBarang($transaction_unique)
    {
        try {
            $po = PurchaseOrder::where('transaction_unique', $transaction_unique)->first();
            if (!$po) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            $po_detail = PurchaseOrderDetail::where('transaction_unique', $transaction_unique)->get();
            $total_po = 0;
            if ($po_detail->count() > 0) {
                foreach ($po_detail as $key => $value) {
                    if ($value->status == 0) {
                        $sparepart = SparePart::where('id', $value->spare_part_id)->first();
                        if ($sparepart) {
                            $sparepart->update([
                                'stock' => $sparepart->stock + $value->quantity
                            ]);
                            $total_po += $value->subtotal;
                        }
                        $value->status = 1;
                        $value->save();
                    }
                }
            }
            $po->update([
                'status' => 'Outstanding',
                'total'  => $total_po
            ]);

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Di Diterima']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function submitPayment(Request $request)
    {
        try {
            $transaction_unique = $request->transaction_unique;
            $po = PurchaseOrder::where('transaction_unique', $transaction_unique)->first();
            if (!$po) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            if ($po->payment_method == 'Kredit') {
                $credit_payment = CreditPayment::where('transaction_unique', $po->transaction_unique)->get();
                $amount = 0;
                if ($credit_payment->count() > 0) {
                    foreach ($credit_payment as $key => $value) {
                        $amount += $value->amount;
                    }
                }
                $balance = $po->total - ($amount + (float)$request->amount);
                $create_credit_payment = CreditPayment::create([
                    'transaction_unique' => $po->transaction_unique,
                    'date'               => date('Y-m-d'),
                    'total'              => $po->total,
                    'amount'             => $request->amount,
                    'balance'            => $balance,
                    'created_by'         => auth()->user()->name,
                    'remark'             => $request->remark
                ]);

                if ($balance != 0) {
                    $po->update([
                        'is_paid' => 1,
                        'status'  => 'Not Paid'
                    ]);
                    return response()->json([
                        'status'  => true,
                        'message' => ['Berhasil Melakukan Pembayaran'],
                        'data'    => [
                            'balance' => $balance
                        ]
                    ]);
                }
            }

            $po_detail = PurchaseOrderDetail::where('transaction_unique', $transaction_unique)->get();
            $status_detail = [];
            if ($po_detail->count() > 0) {
                foreach ($po_detail as $key => $value) {
                    $subtotal = $value->subtotal;
                    $quantity = $value->quantity;
                    $sparepart = SparePart::where('id', $value->spare_part_id)->first();
                    $stock = ($sparepart->stock) ? $sparepart->stock : 0;
                    $selling_price = ($sparepart->selling_price) ? $sparepart->selling_price : 0;
                    $hpp = ($subtotal + ($stock * $selling_price)) / ($stock + $quantity);

                    if ($sparepart) {
                        $sparepart->update([
                            'buying_price' => $hpp,
                            'profit'       => $selling_price - $hpp
                        ]);
                    }
                    $status_detail[] =  $value->status;
                }
            }

            $po_code = (new \App\Helpers\GlobalGenerateCodeHelper())->generateTransactionCode();

            if (count($status_detail) > 0 && in_array(0, $status_detail) || in_array(null, $status_detail)) {
                $po->update([
                    'status' => 'On Order',
                ]);
            } else {
                $po->update([
                    'status' => 'Paid',
                ]);
            }

            if($po->transaction_code){
                $po->update([
                    'is_paid'          => 1
                ]);
            }else{
                $po->update([
                    'is_paid'          => 1,
                    'transaction_code' => $po_code
                ]);
            }
            
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Di Update']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function detailPo($transaction_unique)
    {
        try {
            $data = PurchaseOrder::with('suppliers', 'details', 'details.sparepart')->where('transaction_unique', $transaction_unique)->first();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['Detail Data PO']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function updatePo(Request $request, $transaction_unique)
    {
        try {
            $validation = Validator::make($request->all(), [
                'invoice_number'        => ['required'],
                'invoice_date'          => ['required', 'date'],
                'payment_method'        => ['required', 'in:Cash,Kredit'],
                'spare_part'             => ['required', 'array'],
                'spare_part.*.quantity'  => ['required'],
                'spare_part.*.per_piece' => ['required']
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $po = PurchaseOrder::where('transaction_unique', $transaction_unique)->first();

            if($po->status == 'Paid'){
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Tidak dapat melakukan edit,Status PO sudah paid']);
            }

            if ($po && !empty($request->spare_part)) {
                $payment_due_date = null;
                if (($request->invoice_date) && strtolower($request->payment_method) == 'kredit') {
                    $invoice_date = Carbon::parse($request->invoice_date);
                    $payment_due_date = $invoice_date->addDays($request->payment_due);
                }
                $po->update([
                    'supplier_id'       => ($request->supplier_id) ? $request->supplier_id : $po->supplier_id,
                    'invoice_number'    => ($request->invoice_number) ? $request->invoice_number : $po->invoice_number,
                    'invoice_date'      => ($request->invoice_date) ? $request->invoice_date : $po->invoice_date,
                    'payment_method'    => ($request->payment_method) ? $request->payment_method : $po->payment_method,
                    // 'payment_due_date'  => ($payment_due_date) ? $payment_due_date : $po->payment_due_date,
                    'payment_due_date'  => ($request->payment_due_date) ? $request->payment_due_date : $po->payment_due_date,
                    'remark'            => ($request->remark) ? $request->remark : $po->remark,
                ]);

                $get_id = [];
                foreach ($request->spare_part as $key => $value) {
                    $get_id[] = $value['id'];
                };

                $deleteNotInPo = PurchaseOrderDetail::where('transaction_unique', $transaction_unique)->whereNotIn('id', $get_id)->delete();

                foreach ($request->spare_part as $key => $value) {
                    $detail_po = PurchaseOrderDetail::where('id', $value['id'])->where('transaction_unique', $transaction_unique)->first();
                    $data_detail = [
                        'spare_part_id'      => ($value['spare_part_id']) ? $value['spare_part_id'] : $detail_po->spare_part_id,
                        'quantity'           => ($value['quantity']) ? $value['quantity'] : $detail_po->quantity,
                        'perpiece'           => ($value['per_piece']) ? $value['per_piece'] : $detail_po->perpiece,
                        'subtotal'           => ($value['quantity'] != 0) ? $value['quantity'] * $value['per_piece'] : 0,
                        'status'             => ($po->status == 'Outstanding') ? 1 : 0,
                    ];
                    if ($detail_po) {
                        $detail_po->update($data_detail);
                    } else {
                        $data_detail = [
                            'transaction_unique' => $transaction_unique,
                            'spare_part_id'      => $value['spare_part_id'],
                            'quantity'           => $value['quantity'],
                            'perpiece'           => $value['per_piece'],
                            'subtotal'           => ($value['quantity'] != 0) ? $value['quantity'] * $value['per_piece'] : 0
                        ];
                        PurchaseOrderDetail::create($data_detail);
                    }
                }

                $po_detail = PurchaseOrderDetail::where('transaction_unique', $transaction_unique)->get();
                $sum_subtotal = 0;
                if ($po_detail->count() > 0) {
                    foreach ($po_detail as $val) {
                        $sum_subtotal += $val->quantity * $val->perpiece;
                    }
                }

                $po->update([
                    'total' => $sum_subtotal
                ]);

                return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Disimpan']);
            }
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function terimaBarangByDetail($transaction_unique, $id)
    {
        try {
            $po = PurchaseOrder::where('transaction_unique', $transaction_unique)->first();
            if (!$po) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            $po_detail = PurchaseOrderDetail::where('transaction_unique', $transaction_unique)->where('id', $id)->first();
            if ($po_detail) {
                $sparepart = SparePart::where('id', $po_detail->spare_part_id)->first();
                if ($sparepart) {
                    $sparepart->update([
                        'stock' => $sparepart->stock + $po_detail->quantity
                    ]);
                    $total_po = $po->total + $sparepart->subtotal;

                    $po->update([
                        'total'  => $total_po
                    ]);
                }

                $po_detail = $po_detail->update([
                    'status' => 1 //if 1 = 'Outstanding',
                ]);
            }

            $status = [];
            $cek_status = PurchaseOrderDetail::where('transaction_unique', $transaction_unique)->get();
            if ($cek_status->count() > 0) {
                foreach ($cek_status as $key => $value) {
                    $status[] = $value->status;
                }
            }

            if (!in_array("Draft", $status)) {
                $po->update([
                    'status'  => 'Outstanding'
                ]);
            }

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Di Terima']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
