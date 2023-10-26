<?php

namespace App\Http\Controllers\Api\SmallTransaction;

use Illuminate\Http\Request;
use App\Models\SmallTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SmallTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function list($start_date = null, $end_date = null)
    {
        try {
            $data = SmallTransaction::orderBy('date', 'desc');
            if($start_date != null && $end_date != null) {
                $data = $data->whereBetween('date', [$start_date, $end_date]);
            }
            $data = $data->get();

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['List Data Small Transaction']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function add(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'date'        => ['required', 'date'],
                'description' => ['required', 'string', 'max:255'],
                'pic'         => ['required', 'string', 'max:255'],
                'status'      => ['required', 'in:Debit,Kredit'],
                'category'    => ['required', 'in:Cost,Sublet,Asset,Kas,Modal,Prive,SPM'],
                'total'       => ['required']
            ]);

            if($validation->fails()){
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $data = [
                'date'          => $request->date,
                'description'   => $request->description,
                'pic'           => $request->pic,
                'status'        => $request->status,
                'category'      => $request->category,
                'total'         => $request->total,
                'created_by'    => auth()->user()->name,
            ];

            SmallTransaction::create($data);

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function detail($id)
    {
        try {
            $data = SmallTransaction::where('id', $id)->first();
            if(!$data){
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['Detail Data Small Transaction']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function update(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'id'          => ['required'],
                'date'        => ['required', 'date'],
                'description' => ['required', 'string', 'max:255'],
                'pic'         => ['required', 'string', 'max:255'],
                'status'      => ['required', 'in:Debit,Kredit'],
                'category'    => ['required', 'in:Cost,Sublet,Asset,Kas,Modal,Prive,SPM'],
                'total'       => ['required']
            ]);

            if($validation->fails()){
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $small_trans = SmallTransaction::where('id', $request->id)->first();
            if(!$small_trans){
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }

            $data = [
                'date'          => ($request->date) ? $request->date : $small_trans->date,
                'description'   => ($request->description) ? $request->description : $small_trans->description,
                'pic'           => ($request->pic) ? $request->pic : $small_trans->pic,
                'status'        => ($request->status) ? $request->status : $small_trans->status,
                'category'      => ($request->category) ? $request->category : $small_trans->category,
                'total'         => ($request->total) ? $request->total : $small_trans->total
            ];

            $small_trans->update($data);

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $small_trans = SmallTransaction::where('id', $id)->first();
            if(!$small_trans){
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Data Tidak Ditemukan']);
            }
            $small_trans->delete();
            
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
