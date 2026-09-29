<?php

namespace Modules\RolePermission\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\User;

class UserColumnPermission extends Model
{
    use HasFactory;

    protected $table = "user_column_permissions";
    protected $guarded = [];

    protected static function newFactory()
    {
        return \Modules\RolePermission\Database\factories\UserColumnPermissionFactory::new();
    }

    public function user()
    {
       return $this->belongsTo(User::class)->withDefault();
    }
}
