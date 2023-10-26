<?php

namespace App\Http\Controllers\Api\Register;

use App\Http\Controllers\Controller;
use App\Models\SellSparepart;
use Illuminate\Http\Request;

class RegisterSellController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function list($start_date = null, $end_date = null)
    {
        try {
            $data = SellSparepart::with('details')
                                ->where('status', 'Closed');
                                
            if($start_date && $end_date){
                $data = $data->whereBetween('closed_at', [$start_date, $end_date]);
            }

            $data = $data->orderBy('updated_at', 'DESC')->get();
            $output = [];
            if($data->count() > 0){
                foreach ($data as $key => $value) {
                    $output[] = [
                        'transaction_code'   => $value->transaction_code,
                        'transaction_unique' => $value->transaction_unique,
                        'customer'           => $value->name,
                        'customer_phone'     => $value->phone,
                        'customer_address'   => $value->address,
                        'name'               => $value->name,
                        'payment_date'       => $value->payment_date,
                        'payment_method'     => $value->payment_method,
                        'payment_gateway'    => $value->payment_gateway,
                        'closed_at'          => $value->closed_at,
                        'closed_by'          => $value->closed_by,
                        'total'              => $value->total
                    ];
                }
            }
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($output, ['List Data Register Penjualan Sparepart']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
