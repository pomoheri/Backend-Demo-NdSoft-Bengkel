<?php

namespace App\Http\Controllers\Api\SparePartTransaction\Purchasing;

use Ramsey\Uuid\Uuid;
use App\Models\Supplier;
use App\Models\SparePart;
use Illuminate\Http\Request;
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
            $data = PurchaseOrder::orderBy('created_at', 'desc')->get();
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

            if($request->supplier_id){
                $supplier = Supplier::where('id', $request->supplier_id)->first();
            }else{
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
            
            if(count($request->spare_part) > 0){
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
                            'grade'         => ($value['is_genuine']) ? 'Genuine' : 'Non Genuine',
                            'category'      => $value['category'],
                            'buying_price'  => $value['buying_price'],
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
                        'subtotal'           => ($value['quantity'] != 0) ? $value['quantity']*$value['per_piece'] : 0
                    ];
                    PurchaseOrderDetail::updateOrCreate($data_detail);
                }
            }

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    private function validatePurchaseOrderRequest(Request $request) {
        $validation = Validator::make($request->all(), [
            'invoice_number'        => ['required'],
            'invoice_date'          => ['required', 'date'],
            'payment_method'        => ['required', 'in:Cash,Kredit'],
            'spare_part'            => ['required', 'array'],
            'spare_part.*.quantity' => ['required'],
            'spare_part.*.per_piece' => ['required'],
            'remark'                => ['nullable', 'string', 'max:255']
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
    private function createOrUpdatePurchaseOrder(Request $request, $supplier) {
        return PurchaseOrder::updateOrCreate([
            'supplier_id'    => $supplier->id,
            'invoice_number' => $request->invoice_number,
            'invoice_date'   => $request->invoice_date,
        ], [
            'status'         => 'Draft',
            'created_by'     => auth()->user()->name,
            'payment_method' => $request->payment_method,
            'remark'         => $request->remark,
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
                   $sparepart = SparePart::where('id', $value->spare_part_id)->first();
                   if ($sparepart) {
                        $sparepart->update([
                            'stock' => $sparepart->stock + $value->quantity
                    ]);
                    $total_po += $value->subtotal;
                   }
                }
            }
            $po->update([
                'status' => 'Outstanding',
                'total'  => $total_po
            ]);
            
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Di Update']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function submitPayment($transaction_unique)
    {
        try {
            $po = PurchaseOrder::where('transaction_unique', $transaction_unique)->first();
            if (!$po) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }
            $po_detail = PurchaseOrderDetail::where('transaction_unique', $transaction_unique)->get();
            // if ($po_detail->count() > 0) {
            //     foreach ($po_detail as $key => $value) {
            //        $sparepart = SparePart::where('id', $value->spare_part_id)->first();
            //        if ($sparepart) {
            //             $sparepart->update([
            //                 'selling_price' => $sparepart->stock + $value->quantity
            //         ]);
            //        }
            //     }
            // }
            $po_code = (new \App\Helpers\GlobalGenerateCodeHelper())->generateTransactionCode();
            $po->update([
                'status' => 'Paid',
                'transaction_code' => $po_code
            ]);
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Di Update']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
