<?php

namespace App\Http\Controllers\Api\Register;

use App\Http\Controllers\Controller;
use App\Models\ServiceInvoice;
use Illuminate\Http\Request;

class RegisterServiceInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function list($start_date = null, $end_date = null)
    {
        try {
            $data = ServiceInvoice::with('workOrder', 'workOrder.vehicle', 'workOrder.vehicle.customer', 'workOrder.vehicle.carType', 'workOrder.vehicle.carType.carBrand')
                ->orderBy('closed_at', 'desc');
            if ($start_date && $end_date) {
                $data = $data->whereBetween('closed_at', [$start_date, $end_date]);
            }

            $data = $data->where('status', 'Closed')->get();
            $output = [];
            if ($data->count() > 0) {
                foreach ($data as $key => $value) {
                    $output[] = [
                        'invoice_no'  => $value->transaction_code,
                        'invoice_date' => $value->created_at,
                        'workorder_no' => ($value->workOrder) ? $value->workOrder->transaction_code : '',
                        'payment_date' => $value->closed_at,
                        'customer'     => ($value->workOrder) ? (($value->workOrder->vehicle) ? (($value->workOrder->vehicle->customer) ? $value->workOrder->vehicle->customer->name : '') : '') : '',
                        'nopol'        => ($value->workOrder) ? (($value->workOrder->vehicle) ? $value->workOrder->vehicle->license_plate : '') : '',
                        'color'        => ($value->workOrder) ? (($value->workOrder->vehicle) ? $value->workOrder->vehicle->color : '') : '',
                        'cartype'      => ($value->workOrder) ? (($value->workOrder->vehicle) ? ($value->workOrder->vehicle->carType ? $value->workOrder->vehicle->carType->name : '') : '') : '',
                        'carbrand'     => ($value->workOrder) ? (($value->workOrder->vehicle) ? ($value->workOrder->vehicle->carType ? (($value->workOrder->vehicle->carType->carBrand) ? $value->workOrder->vehicle->carType->carBrand->name : '') : '') : '') : '',
                        'total'        => ($value->workOrder) ? $value->workOrder->total : '',
                        'closed_by'    => $value->closed_by,
                    ];
                }
            }
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['List Data Register Service Invoice']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
