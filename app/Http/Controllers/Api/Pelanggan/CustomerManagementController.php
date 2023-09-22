<?php

namespace App\Http\Controllers\Api\Pelanggan;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CustomerManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function list()
    {
        try {
            $data = Customer::orderBy('name', 'ASC')->get();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data,['List Data Customer']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function add(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'name' => ['required','string', 'max:255'],
                'email' => ['nullable','string', 'email'],
                'phone' => ['required','string', 'max:15'],
                'address' => ['required','string', 'max:255'],
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $code_customer = (new \App\Helpers\GlobalGenerateCodeHelper())->generateCustomerCode();

            $data = [
                'code'      => $code_customer,
                'name'      => $request->name,
                'email'     => $request->email,
                'phone'     => $request->phone,
                'address'   => $request->address,
                'created_by' => auth()->user()->name
            ];

            Customer::updateOrCreate($data);
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Berhasil Menyimpan Data']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function edit(Customer $customer)
    {
        try {
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($customer, ['Data Customer']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function update(Request $request, Customer $customer)
    {
        try {
            $validation = Validator::make($request->all(), [
                'name' => ['required','string', 'max:255'],
                'email' => ['nullable','string', 'email'],
                'phone' => ['required','string', 'max:15'],
                'address' => ['required','string', 'max:255'],
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $customer->update([
                'name'      => $request->name,
                'email'     => $request->email,
                'phone'     => $request->phone,
                'address'   => $request->address,
                'updated_by' => auth()->user()->name
            ]);
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Berhasil Menyimpan Data']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function delete(Customer $customer)
    {
        try {
            $customer->delete();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Berhasil Menghapus Data']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function detail($id)
    {
        try {
            $customer = Customer::with('vehicle', 'vehicle.carType')->where('id', $id)->first();
            if($customer){
                return (new \App\Helpers\GlobalResponseHelper())->sendResponse($customer, ['detail Data']);
            }else{
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data tidak di temukan']);
            }
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
