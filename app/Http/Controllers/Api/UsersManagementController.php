<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Roles;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsersManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function list()
    {
        try {
            $users = User::query();
            $role_super_admin = Roles::where('name', 'Super Admin')->first()->id;
            if (auth()->user()->role_id != $role_super_admin) {
                $users = $users->whereNot('role_id', $role_super_admin);
            }
            $users = $users->orderBy('name', 'asc')->get();
            
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($users, ['Data List Users']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function add(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'name'     => ['required', 'unique:users,name'],
                'username' => ['required', 'unique:users,username'],
                'email'    => ['required', 'unique:users,email'],
                'role_id'  => ['required']
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $data = [
                'name'     => $request->name,
                'username' => $request->username,
                'email'    => $request->email,
                'role_id'  => $request->role_id,
                'password' => Hash::make('password123'),
                'phone'    => $request->phone,
                'address'  => $request->address,
                'is_active' => $request->is_active
            ];

            User::updateOrCreate($data);

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([],['User Berhasil di Create']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function detail(User $user)
    {
        try {
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($user,['Data Detail User']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            $validation = Validator::make($request->all(), [
                'name'     => ['required', 'unique:users,name,'.$user->id],
                'username' => ['required', 'unique:users,username,'.$user->id],
                'email'    => ['required', 'unique:users,email,'.$user->id],
                'role_id'  => ['required']
            ]);

            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $user->update([
                    'name'     => $request->name,
                    'username' => $request->username,
                    'email'    => $request->email,
                    'role_id'  => $request->role_id,
                    'phone'    => $request->phone,
                    'address'  => $request->address,
                    'is_active' => $request->is_active
            ]);

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Di Update']);

        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function delete(User $user)
    {
        try {
            $user->delete();
            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Data Berhasil Di Hapus']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
