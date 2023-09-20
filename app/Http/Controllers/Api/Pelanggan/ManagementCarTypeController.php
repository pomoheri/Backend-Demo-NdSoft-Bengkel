<?php

namespace App\Http\Controllers\Api\Pelanggan;

use App\Models\Cartype;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class ManagementCarTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function list()
    {
        try {
            $data = Cartype::with('CarBrand')->orderBy('name', 'asc')->get();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['List Data Tipe Mobil']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function add(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'car_brand_id' => ['required','int'],
                'name' => ['required','string', 'max:255'],
                'type' => ['required', 'string','unique:car_type,type'],
                'cc'   => ['nullable','string','max:255'],
                'engine_type' => ['nullable', 'string', 'max:255']
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $data = [
                'car_brand_id' => $request->car_brand_id,
                'name'         => $request->name,
                'type'         => $request->type,
                'cc'           => $request->cc,
                'engine_type'  => $request->engine_type
            ];

            Cartype::create($data);
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function detail(Cartype $car_type)
    {
        try {
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($car_type, ['Detail Data Car Type']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function update(Request $request, Cartype $car_type)
    {
        try {
            $validation = Validator::make($request->all(), [
                'car_brand_id' => ['required','int'],
                'name' => ['required','string', 'max:255'],
                'type' => ['required', 'string','unique:car_type,type,'.$car_type->id],
                'cc'   => ['nullable','string','max:255'],
                'engine_type' => ['nullable', 'string', 'max:255']
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $car_type->update([
                'car_brand_id' => $request->car_brand_id,
                'name'         => $request->name,
                'type'         => $request->type,
                'cc'           => $request->cc,
                'engine_type'  => $request->engine_type
            ]);
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Berhasil Menyimpan Data']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function delete(Cartype $car_type)
    {
        try {
           $car_type->delete();
           return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Berhasil Menghapus Data']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
