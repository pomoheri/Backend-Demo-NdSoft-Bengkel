<?php

namespace App\Http\Controllers\Api\SparePartTransaction\Purchasing;

use App\Models\SparePart;
use Ramsey\Uuid\Uuid;
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
            $validation = Validator::make($request->all(), [
                'supplier_id'    => ['required'],
                'invoice_number' => ['required'],
                'invoice_date'   => ['required', 'date']
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $partNumber = (new \App\Helpers\GlobalGenerateCodeHelper())->generateSparePartCode();

            $data = [
                'supplier_id' => $request->supplier_id,
                'invoice_number' => $request->invoice_number,
                'invoice_date'   => $request->invoice_date,
                'status'         => 'Draft',
                'created_by'     => auth()->user()->name
            ];

            PurchaseOrder::updateOrCreate($data);

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function addDetail(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'transaction_unique' => ['required'],
                'spare_part'         => ['required', 'array'],
                'spare_part.*.spare_part_id' => ['required'],
                'spare_part.*.quantity'    => ['required'],
                'spare_part.*.subtotal'    => ['required'],
                'payment_method'       => ['required'],
                'remark'               => ['nullable', 'string']
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $check_po = PurchaseOrder::where('transaction_unique', $request->transaction_unique)->first();
            if (!$check_po) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data PO Tidak ditemukan']);
            }

            $check_po->update([
                'status'           => 'Outstanding',
                'transaction_code' => Uuid::uuid4()->toString(),
                'payment_method'   => $request->payment_method,
                'updated_by'       => auth()->user()->name
            ]);

            $total_po_rp = 0;
            if(count($request->spare_part) > 0){
                foreach ($request->spare_part as $key => $value) {
                    $data = [
                        'transaction_unique' => $request->transaction_unique,
                        'spare_part_id'      => $value['spare_part_id'],
                        'quantity'           => $value['quantity'],
                        'subtotal'           => $value['subtotal'],
                        'perpiece'           => ($value['quantity'] != 0) ? $value['subtotal']/$value['quantity'] : 0
                    ];
                    PurchaseOrderDetail::updateOrCreate($data);
                    $stock_spare_part = SparePart::where('id', $value['spare_part_id'])->first();
                    if ($stock_spare_part) {
                        $stock_spare_part->update([
                            'stock'      => $stock_spare_part->stock + $value['quantity'],
                            'updated_by' => auth()->user()->name
                        ]);
                    }
                    $total_po_rp += $value['subtotal'];
                }
            }

            $check_po->update([
                'status'           => 'Outstanding',
                'transaction_code' => Uuid::uuid4()->toString(),
                'payment_method'   => $request->payment_method,
                'updated_by'       => auth()->user()->name,
                'total'            => $total_po_rp,
                'remark'           => $request->remark
            ]);
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Disimpan']);

        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function submitPayment(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'transaction_unique' => ['required'],
                'transaction_code'   => ['required'],
                'id'                 => ['required']
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
