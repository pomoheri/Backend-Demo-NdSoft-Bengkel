<?php

namespace App\Http\Controllers\Api;

use App\Models\Menus;
use App\Models\Roles;
use App\Models\RoleMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MenuManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function list()
    {
        try {
            $menus = Menus::with('parent','children')->orderBy('name', 'ASC')->get();
            $data = [];
            if ($menus->count() > 0) {
                foreach($menus->whereNull('parent_id')->sortBy('id') as $menus){
                    $data['menus'][] = $menus;
                 }
            }
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['Data List Menu']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function add(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'name' => ['required', 'unique:menus,name'],
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $menus = Menus::query();
            if ($request->parent_id) {
                $menus = $menus->where('parent_id', $request->parent_id);
            }else{
                $menus = $menus->whereNull('parent_id');
            }
            $max_order = $menus->max('order');

            $data = [
                'parent_id' => ($request->parent_id) ? $request->parent_id : null,
                'name'      => $request->name,
                'icon'      => $request->icon,
                'url'       => $request->url,
                'order'     => ($max_order != null) ? $max_order+1 : 1
            ];

            Menus::updateOrCreate($data);

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Menu Berhasil Disimpan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function delete(Menus $menus)
    {
        try {
            if ($menus->children) {
                foreach ($menus->children as $value) {
                    $value->delete();
                }
            }
            $menus->delete();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Menu Berhasil Dihapus']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
    public function detail(Menus $menus)
    {
        try {
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($menus, ['Data Detail Menu Berhasil Ditampilkan']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function update(Request $request, Menus $menus)
    {
        try {
            $validation = Validator::make($request->all(), [
                'name' => ['required', 'unique:menus,name,'.$menus->id]
            ]);
            
            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $max_order = Menus::query();
            if ($request->parent_id && $menus->parent_id == $request->parent_id) {
                $max_order = $menus->order;
            }else if($request->parent_id && $menus->parent_id != $request->parent_id) {
                $max_order = $max_order->where('parent_id', $request->parent_id)->max('order');
            }else{
                $max_order = $max_order->whereNull('parent_id')->max('order');
            }

            $menus->update([
                'parent_id' => ($request->parent_id) ? $request->parent_id : null,
                'name'      => $request->name,
                'icon'      => $request->icon,
                'url'       => $request->url,
                'order'     => ($max_order != null) ? $max_order+1 : 1
            ]);

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($menus, ['Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function roleMenuList(Menus $menus)
    {
        try {
            $role = Roles::all();
            $tamp = [];
            if ($role->count() > 0) {
                foreach ($role as $key => $value) {
                    $role_menu = RoleMenu::where('role_id', $value->id)->where('menu_id', $menus->id)->first();
                    $tamp[] = [
                        'role_id' => $value->id,
                        'role' => $value->name,
                        'value' => ($role_menu) ? true : false
                    ];
                }
            }
            $data = [
                'menu'  => $menus->name,
                'lists' => $tamp,
            ];
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['List Role Menu List']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function setRoleMenu(Request $request, Menus $menus)
    {
        try {
            $arr = [];
            if ($request->has('data') && $request->filled('data')) {
                foreach ($request->data as $k => $v) {
                    $arr['menu_id'] = $menus->id;
                    $arr['role_id'] = $v['role_id'];
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
