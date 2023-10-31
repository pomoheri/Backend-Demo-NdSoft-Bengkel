<?php

namespace App\Http\Controllers\Api\Service;

use App\Http\Controllers\Controller;
use App\Models\Labour;
use Illuminate\Http\Request;
use Validator;

class LabourController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function list()
    {
        try {
            $data = Labour::orderBy('labour_code', 'ASC')->get();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['List Data Labour']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function add(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'labour_code' => ['required', 'unique:labour,labour_code'],
                'frt'         => ['required', 'max:255'],
                'price'       => ['required']
            ]);

            if($validation->fails()){
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $data = [
                'labour_code' => $request->labour_code,
                'frt'         => $request->frt, 
                'price'       => $request->price,
                'created_by'  => auth()->user()->name
            ];

            Labour::create($data);

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function detail($id)
    {
        try {
            $data = Labour::where('id', $id)->first();
            if(!$data) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['Data Detail Labour']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'id'          => ['required', 'integer'],
                'labour_code' => ['required', 'unique:labour,labour_code,'.$request->id],
                'frt'         => ['required', 'max:255'],
                'price'       => ['required']
            ]);

            if($validation->fails()){
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $labour = Labour::where('id', $request->id)->first();
            if(!$labour) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            $data = [
                'labour_code' => $request->labour_code,
                'frt'         => $request->frt, 
                'price'       => $request->price,
                'updated_by'  => auth()->user()->name
            ];

            $labour->update($data);
            
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function delete(Labour $labour)
    {
        try {
            $labour->delete();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
