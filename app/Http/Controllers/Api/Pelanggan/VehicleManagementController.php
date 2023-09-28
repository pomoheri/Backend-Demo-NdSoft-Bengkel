<?php

namespace App\Http\Controllers\Api\Pelanggan;

use Validator;
use App\Models\Vehicle;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VehicleManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function list()
    {
        try {
            $vehicles = Vehicle::with('carType')
                ->join('customer', 'vehicle.customer_id', '=', 'customer.id')
                ->orderBy('customer.code')
                ->select('vehicle.*', 'customer.code as kode_customer', 'customer.name as nama_customer', 'customer.phone as phone')
                ->get();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($vehicles, ['List Data Vehicle']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function addOldCustomer(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'car_type_id'  => ['required'],
                'customer_id'  => ['required'],
                'license_plate' => ['required'],
                'year'         => ['required', 'integer'],
                'last_km'      => ['required', 'integer'],
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }
            $data = [
                'customer_id'   => $request->customer_id,
                'car_type_id'   => $request->car_type_id,
                'license_plate'  => $request->license_plate,
                'engine_no'     => $request->engine_no,
                'chassis_no'     => $request->chassis_no,
                'color'         => $request->color,
                'year'          => $request->year,
                'last_km'       => $request->last_km,
                'transmission'  => $request->transmission,
                'created_by'    => auth()->user()->name
            ];

            Vehicle::updateOrCreate($data);
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Berhasil Menyimpan Data']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function addNewCustomer(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'car_type_id'  => ['required'],
                'license_plate' => ['required'],
                'year'         => ['required', 'integer'],
                'last_km'      => ['required', 'integer'],

                'name' => ['required', 'string', 'max:255'],
                'email' => ['nullable', 'string', 'email'],
                'phone' => ['required', 'string', 'max:15'],
                'address' => ['required', 'string', 'max:255'],
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }
            $code_customer = (new \App\Helpers\GlobalGenerateCodeHelper())->generateCustomerCode();

            $data_customer = [
                'code'      => $code_customer,
                'name'      => $request->name,
                'email'     => $request->email,
                'phone'     => $request->phone,
                'address'   => $request->address,
                'created_by' => auth()->user()->name
            ];

            $customer = Customer::updateOrCreate($data_customer);

            $data = [
                'customer_id'    => $customer->id,
                'car_type_id'    => $request->car_type_id,
                'license_plate'  => $request->license_plate,
                'engine_no'      => $request->engine_no,
                'chassis_no'     => $request->chassis_no,
                'color'          => $request->color,
                'year'           => $request->year,
                'last_km'        => $request->last_km,
                'transmission'   => $request->transmission,
                'created_by'     => auth()->user()->name
            ];

            Vehicle::updateOrCreate($data);
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Berhasil Menyimpan Data']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function delete(Vehicle $vehicle)
    {
        try {
            $vehicle->delete();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Berhasil Menghapus Data']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function edit(Vehicle $vehicle)
    {
        try {
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($vehicle, ['Data Detail Vehicle']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function update(Request $request, Vehicle $vehicle)
    {
        try {
            $validation = Validator::make($request->all(), [
                'car_type_id'  => ['required'],
                'customer_id'  => ['required'],
                'license_plate' => ['required'],
                'year'         => ['required', 'integer'],
                'last_km'      => ['required', 'integer'],
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }
            $vehicle->update([
                'customer_id'    => $request->customer_id,
                'car_type_id'    => $request->car_type_id,
                'license_plate'  => $request->license_plate,
                'engine_no'      => $request->engine_no,
                'chassis_no'     => $request->chassis_no,
                'color'          => $request->color,
                'year'           => $request->year,
                'last_km'        => $request->last_km,
                'transmission'   => $request->transmission,
                'updated_by'     => auth()->user()->name
            ]);

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Berhasil Menyimpan Data']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
