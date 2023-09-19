<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleMenu extends Model
{
    protected $table = 'role_menu';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = true;
    protected $casts = [
        'role_id' => 'int',
        'menu_id' => 'int'
    ];
    protected $fillable = [
        'role_id',
        'menu_id'
    ];

    public function menu(){
        return $this->belongsTo(Menu::class, 'menu_id');
    }
    public function roles(){
        return $this->belongsTo(Roles::class, 'role_id');
    }
}
