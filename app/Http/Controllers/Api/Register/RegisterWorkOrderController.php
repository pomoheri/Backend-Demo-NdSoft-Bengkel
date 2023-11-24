<?php

namespace App\Http\Controllers\Api\Register;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use Illuminate\Http\Request;

class RegisterWorkOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function list($start_date = null, $end_date = null)
    {
        try {
            $data = WorkOrder::with('vehicle', 'vehicle.customer', 'vehicle.carType', 'vehicle.carType.carBrand')->orderBy('created_at', 'desc');
            if ($start_date && $end_date) {
                $data = $data->whereBetween('created_at', [$start_date, $end_date]);
            }

            $data = $data->where('status', 'Closed')->get();
            $output = [];
            if ($data->count() > 0) {
                foreach ($data as $key => $value) {
                    $output[] = [
                        'date'        => $value->created_at,
                        'no_wo'       => $value->transaction_code,
                        'customer'    => ($value->vehicle) ? (($value->vehicle->customer) ? $value->vehicle->customer->name : null) : null,
                        'car'         => ($value->vehicle) ? (($value->vehicle->carType) ? $value->vehicle->carType->name : null) : null,
                        'license_plate' => ($value->vehicle) ? $value->vehicle->license_plate : null,
                        'total'       => $value->total,
                        'status'      => $value->status,
                        'created_by'  => $value->created_by,
                        'created_at'  => $value->created_at
                    ];
                }
            }
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['List Data Register Workorder']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
