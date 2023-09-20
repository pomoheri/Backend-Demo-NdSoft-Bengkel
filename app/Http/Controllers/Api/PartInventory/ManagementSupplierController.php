<?php

namespace App\Http\Controllers\Api\PartInventory;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class ManagementSupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function list()
    {
        try {
            $data = Supplier::orderBy('name', 'ASC');
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['List Data Supplier']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function add(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'supplier_code' => ['required','max:100','unique:supplier,supplier_code'],
                'name'          => ['required','string','max:255','unique:supplier,name'],
                'address'       => ['required','string', 'max:255'],
                'pic'           => ['required', 'max:100']
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $data = [
                'supplier_code' => $request->supplier_code,
                'name'          => $request->name,
                'address'       => $request->address,
                'pic'           => $request->pic,
                'phone'         => $request->phone,
                'created_by'    => auth()->user()->name
            ];

            Supplier::updateOrCreate($data);
            
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([],['Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function detail(Supplier $supplier)
    {
        try {
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($supplier, ['Data Detail Supplier']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function update(Request $request, Supplier $supplier)
    {
        try {
            $validation = Validator::make($request->all(), [
                'supplier_code' => ['required','max:100','unique:supplier,supplier_code,'.$supplier->id],
                'name'          => ['required','string','max:255','unique:supplier,name,'.$supplier->id],
                'address'       => ['required','string', 'max:255'],
                'pic'           => ['required', 'max:100']
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }
            $supplier->update([
                'supplier_code' => $request->supplier_code,
                'name'          => $request->name,
                'address'       => $request->address,
                'pic'           => $request->pic,
                'phone'         => $request->phone,
                'updated_by'    => auth()->user()->name
            ]);
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([],['Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function delete(Supplier $supplier)
    {
        try {
            $supplier->delete();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([],['Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
