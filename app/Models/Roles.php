<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
   protected $table = 'roles';
   protected $fillable = [
        'name'
   ];

   public function users()
   {
        return $this->hasMany(User::class, 'role_id');
   }

   public function menus()
    {
        return $this->belongsToMany(Menus::class, 'role_menu', 'role_id', 'menu_id');
    }
}

