<?php

namespace App\Http\Controllers\Api;

use App\Models\Menus;
use App\Models\Roles;
use App\Models\CarBrand;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MasterDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function listRole()
    {
        try {
            $role = Roles::orderBy('name', 'ASC')->get();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($role, ['Data Master Data Role']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function listMenu()
    {
        try {
            $menus = Menus::orderBy('name', 'ASC')->get();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($menus, ['Data List Menu']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function listCarBrand()
    {
        try {
            $car_brand = CarBrand::orderBy('name', 'ASC')->get();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($car_brand, ['Data List Car Brand']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function listTeknisi()
    {
        try {
            $role_teknisi = Roles::where('name', 'like', '%Teknisi%')->first();
            if($role_teknisi){
                $teknisi = User::select('id','name')->where('role_id', $role_teknisi->id)->orderBy('name')->get();
            }else{
                $teknisi = [];
            }
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($teknisi, ['Data Teknisi']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
