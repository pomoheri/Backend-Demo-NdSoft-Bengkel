<?php

namespace App\Http\Controllers\Api\Register;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Http\Controllers\Controller;

class RegisterSparepartPurchasingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function list()
    {
        try {
            $data = PurchaseOrder::with('suppliers')
                ->where('is_paid', 1)
                ->where('status', 'Paid')
                ->orderBy('updated_at', 'DESC')
                ->get();

            $output = [];
            if ($data->count() > 0) {
                foreach ($data as $key => $value) {
                    $output[] = [
                        'id'                 => $value->id,
                        'transaction_code'   => $value->transaction_code,
                        'transaction_unique' => $value->transaction_unique,
                        'invoice_number'     => $value->invoice_number,
                        'invoice_date'       => $value->invoice_date,
                        'closed_at'          => $value->closed_at,
                        'closed_by'          => $value->closed_by,
                        'created_at'         => $value->created_at,
                        'supplier'           => ($value->suppliers) ? $value->suppliers->name : '',
                        'kontak'             => ($value->suppliers) ? $value->suppliers->phone : '',
                        'total'              => $value->total
                    ];
                }
            }
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($output, ['List Data Register Pembelian Sparepart']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
