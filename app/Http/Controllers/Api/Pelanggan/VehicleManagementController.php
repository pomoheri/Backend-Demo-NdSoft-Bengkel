<?php

namespace App\Http\Controllers\Api\Pelanggan;

use Validator;
use Carbon\Carbon;
use App\Models\Vehicle;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PDF;

class VehicleManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function list()
    {
        try {
            $vehicles = Vehicle::with('carType', 'carType.carBrand')
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

    public function historyVehicle($id)
    {
        try {
            $vehicle = Vehicle::with('workOrder', 'customer','carType','carType.CarBrand', 'workOrder.serviceInvoice')
                            ->where('id', $id)
                            ->first();
            if(!$vehicle){
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            $detail_customer = [];
            if($vehicle->customer){
                $detail_customer = [
                    'code'    => $vehicle->customer->code,
                    'name'    => $vehicle->customer->name,
                    'email'   => $vehicle->customer->email,
                    'phone'   => $vehicle->customer->phone,
                    'address' => $vehicle->customer->address,
                    'created_at' => Carbon::parse($vehicle->customer->created_at)->format('Y-m-d'),
                    'created_by' => $vehicle->customer->created_by
                ];
            }

            $detail_vehicle = [
                'car_brand'     => ($vehicle->carType) ? ($vehicle->carType->carBrand ? $vehicle->carType->carBrand->name : '') : '',
                'car'           => ($vehicle->carType) ? $vehicle->carType->name : '',
                'color'         => $vehicle->color,
                'year'          => $vehicle->year,
                'license_plate' => $vehicle->license_plate,
                'chassis_no'    => $vehicle->chassis_no,
                'engine_no'     => $vehicle->engine_no,
                'transmission'  => $vehicle->transmission,
                'created_by'    => $vehicle->created_by,
                'created_at'    => Carbon::parse($vehicle->created_at)->format('Y-m-d')
            ];

            $detail_transaksi = [];
            if($vehicle->workOrder->count() > 0){
                foreach($vehicle->workOrder as $item){
                    $detail_transaksi[] = [
                        'transaction_unique' => $item->transaction_unique,
                        'invoice_code'       => ($item->serviceInvoice) ? $item->serviceInvoice->transaction_code : '',
                        'invoice_date'       => ($item->serviceInvoice) ? Carbon::parse($item->serviceInvoice->created_at)->format('Y-m-d') : '',
                        'wo_code'            => $item->transaction_code,
                        'wo_date'            => Carbon::parse($item->created_at)->format('Y-m-d'),
                        'km'                 => $item->km,
                        'technician'         => $item->technician
                    ];
                }
            }

            $output = [
                'detail_customer'  => $detail_customer,
                'detail_vehicle'   => $detail_vehicle,
                'detail_transaksi' => $detail_transaksi
            ];


            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($output, ['Data History Vehicle']);

        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function getPdfHistoryVehicle($id)
    {
        try {
            $vehicle = Vehicle::with('workOrder', 'customer','carType','carType.CarBrand', 'workOrder.serviceInvoice', 'workOrder.serviceRequest', 'workOrder.sellSparepartDetail','workOrder.sellSparepartDetail.sparepart')
                            ->where('id', $id)
                            ->first();
            if(!$vehicle){
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            $data = [
                'vehicle'  => $vehicle,
                'carType'  => $vehicle->carType,
                'carBrand' => ($vehicle->carType) ? $vehicle->carType->carBrand : null
            ];

            $pdf = PDF::loadView('documents.history-transaction-vehicle', $data)->setPaper('a4', 'landscape');

            $pdf_file = $pdf->output();

            $directory = 'public/history-transaction-vehicle/'.$vehicle->license_plate.'.pdf';

            \Storage::put($directory,$pdf_file);

            $pdf_url = env('APP_URL').\Storage::url($directory);

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($pdf_url,['Data Berhasil Di Generate']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
