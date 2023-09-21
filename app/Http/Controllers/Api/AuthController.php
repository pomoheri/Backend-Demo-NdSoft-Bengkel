<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Menus;
use App\Models\RoleMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'username'    => ['required', 'string', 'max:255'],
            'password'    => ['required', 'min:8']
        ]);

        if ($validation->fails()) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
        }

        if (!Auth::attempt($request->only('username', 'password')))
        {
            return (new \App\Helpers\GlobalResponseHelper())->sendError(['Username Atau Password Salah']);
        }

        $user = User::where('username', $request['username'])->firstOrFail();

        $menu = [];
        if($user && $user->roles){
            if($user->is_active != 1){
                return (new \App\Helpers\GlobalResponseHelper())->sendError(['Akun Anda Belum Di Aktifkan Oleh Admin']);
            }
            
            $role_id = $user->roles->id;
            $roleMenu = RoleMenu::where('role_id', $role_id)->get();
            if($roleMenu->count() > 0) {
                foreach ($roleMenu as $key => $value) {
                    $menu[] = Menus::with('children')->where('id', $value->menu_id)->first();
                }
            }
        }
        
        $data = [];
        if (count($menu) > 0){ 
            $filteredUserMenu = array_filter($menu, function ($menu) {
                return $menu->parent_id === null;
            });
        
            usort($filteredUserMenu, function ($a, $b) {
                return $a->id - $b->id;
            });
        
            $data['menus'] = $filteredUserMenu;
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $data = [
            'access_token'  => $token,
            'menu'          => $data,
            'token_type'    => 'Bearer'
        ];

        return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['Berhasil Login']);
    }

    public function getUserProfile()
    {
        try {
            $user = User::with('roles')->where('id', auth()->user()->id)->first();

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($user, ['Data User Profile']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function register(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'username'  => ['required','string','min:2','max:255','unique:users,username'],
                'password'  => ['required','string','min:8', 'confirmed'],
                'email'     => ['required','string', 'email','max:255','unique:users,email'],
                'phone'     => ['required','string']            
            ]);
    
            if ($validation->fails()) {
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $data_users = [
                'name'     => $request->name,
                'username' => $request->username,
                'email'    => $request->email,
                'phone'    => $request->phone,
                'address'  => $request->address,
                'role_id'  => 4,
                'is_active'  => 0,
                'password'  =>  Hash::make($request->password),
                'remember_token' => $this->generateRandomString(25)
            ];

            User::updateOrCreate($data_users);

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse([], ['Berhasil Registrasi']);

        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    protected function generateRandomString($length = 15) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function defaultMenu()
    {
        try {
            $data = [];
            $user = User::with('roles', 'roles.menus.parent', 'roles.menus.children')->where('id', auth()->user()->id)->first();
            if($user && $user->roles){
                $data['roles'] = $user->roles->name;
                foreach($user->roles->menus->whereNull('parent_id')->sortBy('id') as $menus){
                   $data['menus'][] = $menus;
                }
            }

            return (new \App\Helpers\GlobalResponseHelper())->sendResponse($data, ['Data Default Menu']);
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return (new \App\Helpers\GlobalResponseHelper())->sendResponse(['access_token' => null], ['Berhasil Logout']);
        
    }
    public function resetPassword(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'old_password' => ['required'],
                'password' => ['required','min:8','string','confirmed'],
                'password_confirmation' => ['required', 'same:password']
            ]);

            if($validation->fails()){
                return (new \App\Helpers\GlobalResponseHelper())->sendError($validation->errors()->all());
            }

            $user = User::where('id', auth()->user()->id)->first();
            
            if ($user) {
                if(!Hash::check($request->old_password,$user->password)){
                    return response()->json([
                        'status' => false,
                        'message' => ['Passwor lama anda salah']
                    ]);
                }
                $user->password = Hash::make($request->password);
                $user->update();
                
                return (new \App\Helpers\GlobalResponseHelper())->sendResponse([],['Berhasil Mengubah Password']);
            }else{
                return (new \App\Helpers\GlobalResponseHelper())->sendError([],['Data Tidak Ditemukan']);
            }
        } catch (\Exception $e) {
            return (new \App\Helpers\GlobalResponseHelper())->sendError($e->getMessage());
        }
    }
}
