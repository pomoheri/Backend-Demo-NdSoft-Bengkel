<?php

namespace App\Http\Controllers\Api;

use App\Models\Menus;
use App\Models\Roles;
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
}
