<?php

namespace App\Http\Controllers\Api\Service;

use App\Models\Estimation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class EstimationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function list(Request $request)
    {
        try {
            $estimation = Estimation::query();
            if(isset($request->start_date) && $request->start_date){
                $estimation = $estimation->where('created_at', '>=', $request->start_date);
            }
            if(isset($request->end_date) && $request->end_date){
                $estimation = $estimation->where('created_at', '<=', $request->end_date);
            }

            $estimation = $estimation->where('status', 'New')->orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function add(Request $request)
    {
        try {
            $validation = Validator::make($request->all(),[
                'vehicle_id' => ['required', 'integer']
            ]);

            if(!$validation->fails()){
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            

        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
