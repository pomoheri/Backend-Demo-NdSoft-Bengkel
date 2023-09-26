<?php

namespace App\Http\Controllers\Api\Pelanggan;

use App\Models\CarBrand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CarBrandManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function list()
    {
        try {
            $data = CarBrand::orderBy('name', 'asc')->get();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['List Data Car Brand']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function add(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'name' => ['required','string', 'max:255','unique:car_brand,name'],
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $data = [
                'name'         => $request->name,
            ];

            CarBrand::create($data);
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function detail(CarBrand $car_brand)
    {
        try {
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($car_brand, ['Detail Data Car Brand']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function update(Request $request, CarBrand $car_brand)
    {
        try {
            $validation = Validator::make($request->all(), [
                'name' => ['required','string', 'max:255', 'unique:car_brand,name,'.$car_brand->id]
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $car_brand->update([
                'name'         => $request->name
            ]);
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Berhasil Menyimpan Data']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function delete(CarBrand $car_brand)
    {
        try {
           $car_brand->delete();
           return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Berhasil Menghapus Data']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
