<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menus extends Model
{
    protected $table = 'menus';
    protected $fillable = [
        'parent_id',
        'name',
        'icon',
        'url',
        'order'
    ];
    public function role_menu()
    {
        return $this->belongsToMany(Roles::class, 'role_menu', 'menu_id', 'role_id')->withTimestamps();
    }

    public function children()
    {
        return $this->hasMany(Menus::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Menus::class, 'parent_id');
    }
}
