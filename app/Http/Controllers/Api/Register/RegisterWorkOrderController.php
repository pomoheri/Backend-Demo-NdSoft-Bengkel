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
            $data = WorkOrder::query();
            $data = $data->with('vehicle', 'vehicle.customer', 'vehicle.carType', 'vehicle.carType.carBrand');
            if ($start_date) {
                $data = $data->whereDate('updated_at', '>=', $start_date);
            }
            if ($end_date) {
                $data = $data->whereDate('updated_at', '<=', $end_date);
            }

            $data = $data->orderBy('updated_at', 'desc')->where('status', 'Closed')->get();
            $output = [];
            if ($data->count() > 0) {
                foreach ($data as $key => $value) {
                    $output[] = [
                        'date'        => $value->created_at,
                        'no_wo'       => $value->transaction_code,
                        'customer'    => ($value->vehicle) ? (($value->vehicle->customer) ? $value->vehicle->customer->name : '') : '',
                        'car_type'    => ($value->vehicle) ? (($value->vehicle->carType) ? $value->vehicle->carType->name : '') : '',
                        'car_brand'   => ($value->vehicle) ? (($value->vehicle->carType) ? (($value->vehicle->carType->carBrand) ? $value->vehicle->carType->carBrand->name : '') : '') : '',
                        'license_plate' => ($value->vehicle) ? $value->vehicle->license_plate : '',
                        'color'       => ($value->vehicle) ? $value->vehicle->color : '',
                        'total'       => $value->total,
                        'status'      => $value->status,
                        'created_by'  => $value->created_by,
                        'created_at'  => $value->created_at
                    ];
                }
            }
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($output, ['List Data Register Workorder']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
