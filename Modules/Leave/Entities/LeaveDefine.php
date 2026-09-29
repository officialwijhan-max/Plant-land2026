<?php

namespace Modules\Leave\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Modules\RolePermission\Entities\Role;
use Modules\Leave\Entities\LeaveType;
use App\User;

class LeaveDefine extends Model
{
    protected $table = "leave_defines";

    protected $guarded = ['id'];

    public function role()
    {
        return $this->belongsTo(Role::class)->withDefault();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function leave_type()
    {
        return $this->belongsTo(LeaveType::class)->withDefault();
    }

}
