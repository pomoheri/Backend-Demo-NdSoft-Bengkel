<?php

namespace App\Http\Controllers\Api\PartInventory;

use App\Models\SparePart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SparePartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function list()
    {
        try {
            $data = SparePart::with('partLocations')->orderBy('name', 'ASC')->get();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['List Data Spare Part']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function add(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'name'          => ['required', 'string', 'max:255', 'unique:spare_part,name'],
                'car_brand_id'  => ['required'],
                'grade'         => ['required', 'in:Genuine, Non Genuine'],
                'category'      => ['required', 'in:Spare Part, Material, Asset'],
                'buying_price'  => ['required'],
                'selling_price' => ['required']
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $partNumber = (new \App\Helpers\GlobalGenerateCodeHelper())->generateSparePartCode();

            $data = [
                'part_number'   => $partNumber,
                'name'          => $request->name,
                'car_brand_id'  => $request->car_brand_id,
                'grade'         => $request->grade,
                'category'      => $request->category,
                'stock'         => ($request->stock) ? $request->stock : '0',
                'buying_price'  => $request->buying_price,
                'selling_price' => $request->selling_price,
                'profit'        => 0,
                'location_id'   => $request->location_id,
                'created_by'    => auth()->user()->name
            ];

            SparePart::updateOrCreate($data);

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function edit(SparePart $spare_part)
    {
        try {
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($spare_part, ['Data Edit Spare Part']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function update(Request $request, SparePart $spare_part)
    {
        try {
            $validation = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255', 'unique:spare_part,name,' . $spare_part->id],
                'car_brand_id'  => ['required'],
                'grade'         => ['required', 'in:Genuine, Non Genuine'],
                'category'      => ['required', 'in:Spare Part, Material, Asset'],
                'buying_price'  => ['required'],
                'selling_price' => ['required']
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $spare_part->update([
                'name'          => $request->name,
                'car_brand_id'  => $request->car_brand_id,
                'grade'         => $request->grade,
                'category'      => $request->category,
                'buying_price'  => $request->buying_price,
                'selling_price' => $request->selling_price,
                'location_id'   => $request->location_id,
                'updated_by'    => auth()->user()->name
            ]);
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
