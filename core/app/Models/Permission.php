<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model {

    public $timestamps = false;

    protected $fillable = ['name', 'group', 'code'];

    public function roles() {
        return $this->belongsToMany(Role::class);
    }

    protected static function boot() {
        parent::boot();
        static::saved(function () {
            \Cache::forget('AllPermissions');
        });
    }

}
