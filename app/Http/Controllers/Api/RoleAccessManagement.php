<?php

namespace App\Http\Controllers\Api;

use App\Models\Menus;
use App\Models\Roles;
use App\Models\RoleMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RoleAccessManagement extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function list()
    {
        try {
            $role = Roles::orderBy('name', 'asc')->get();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($role, ['Data List Role']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function add(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'name' => ['required', 'string', 'unique:roles,name']
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            Roles::updateOrCreate([
                'name' => $request->name
            ]);
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Berhasil Menyimpan Data']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function detail(Roles $role)
    {
        try {
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($role, ['Detail Data Role']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function update(Request $request, Roles $role)
    {
        try {
            $validation = Validator::make($request->all(), [
                'name' => ['required', 'unique:roles,name,'.$role->id]
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $role->update([
                'name' => $request->name
            ]);

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Berhasil Update Data']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function delete(Roles $role)
    {
        try {
            $role->delete();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Berhasil Delete Data']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function roleMenuList(Roles $role)
    {
        try {
            $menu = Menus::all();
            $tamp = [];
            if ($menu->count() > 0) {
                foreach ($menu as $key => $value) {
                    $role_menu = RoleMenu::where('menu_id', $value->id)->where('role_id', $role->id)->first();
                    $tamp[] = [
                        'menu_id' => $value->id,
                        'menu' => $value->name,
                        'value' => ($role_menu) ? true : false
                    ];
                }
            }
            $data = [
                'role'  => $role,
                'lists' => $tamp,
            ];
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['List Role Menu List']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function setRoleMenu(Request $request, Roles $role)
    {
        try {
            $arr = [];
            if ($request->has('data') && $request->filled('data')) {
                foreach ($request->data as $k => $v) {
                    $arr['role_id'] = $role->id;
                    $arr['menu_id'] = $v['menu_id'];
                    if($v['value'] === true){
                        
                        $rolemenu = RoleMenu::where($arr)->first();
                        if ($rolemenu) {
                            $rolemenu->update($arr);
                        }else{
                            $rolemenu = RoleMenu::create($arr);
                        }
                    }else{
                        RoleMenu::where($arr)->delete();
                    }
                }
            }
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([],['Berhasil Set Role']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
