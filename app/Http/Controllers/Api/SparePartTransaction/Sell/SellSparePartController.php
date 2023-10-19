<?php

namespace App\Http\Controllers\Api\SparePartTransaction\Sell;

use App\Http\Controllers\Controller;
use App\Models\SellSparepart;
use Illuminate\Http\Request;

class SellSparePartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function list()
    {
        try {
            $data = SellSparepart::with('detail', 'detail.sparepart')->orderBy('created_at', 'desc')->get();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['List Data Sell Sparepart']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
