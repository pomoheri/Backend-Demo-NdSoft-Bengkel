<?php

namespace App\Http\Controllers\Api\Register;

use App\Http\Controllers\Controller;
use App\Models\SmallTransaction;
use Illuminate\Http\Request;

class RegisterSmallTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function list($start_date = null, $end_date = null)
    {
        try {
            $data = SmallTransaction::orderBy('date', 'desc');
            if($start_date && $end_date){
                $data = $data->whereBetween('date', [$start_date, $end_date]);
            }

            $data = $data->get();
            $output = [];
            if($data->count() > 0){
                foreach ($data as $key => $value) {
                    $output[] = [
                        'date'     => $value->tanggal,
                        'description' => $value->description,
                        'pic'         => $value->pic,
                        'status'      => $value->status,
                        'category'    => $value->category,
                        'total'       => $value->total,
                        'created_by'  => $value->created_by,
                        'created_at'  => $value->created_at
                    ];
                }
            }
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($output, ['List Data Register Small Transaction']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
