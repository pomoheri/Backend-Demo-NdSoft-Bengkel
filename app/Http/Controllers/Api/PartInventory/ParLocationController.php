<?php

namespace App\Http\Controllers\Api\PartInventory;

use App\Models\PartLocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ParLocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function list()
    {
        try {
            $data = PartLocation::orderBy('code', 'ASC')->get();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['List Data Part Location']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function add(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'code'          => ['required', 'string', 'max:150', 'unique:part_location,code'],
                'location'       => ['required', 'string', 'max:255']
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $data = [
                'code'      => $request->code,
                'location'  => $request->location
            ];

            PartLocation::updateOrCreate($data);

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function detail(PartLocation $part_location)
    {
        try {
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($part_location, ['Data Detail Part Location']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function update(Request $request, PartLocation $part_location)
    {
        try {
            $validation = Validator::make($request->all(), [
                'code'          => ['required', 'string', 'max:150', 'unique:part_location,code,' . $part_location->id],
                'location'      => ['required', 'string', 'max:255']
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }
            $part_location->update([
                'code'      => $request->code,
                'location'  => $request->location
            ]);
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function delete(PartLocation $part_location)
    {
        try {
            $part_location->delete();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
