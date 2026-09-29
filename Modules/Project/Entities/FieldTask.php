<?php

namespace Modules\Project\Entities;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class FieldTask extends Pivot
{
    use LogsActivity;

    protected $fillable = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnlyDirty()
        ->submitEmptyLogs()
        ->logExcept(['updated_at'])
        ->useLogName("field_task");
        // Chain fluent methods for configuration options
    }  


    public function option(){
        return $this->belongsTo(FieldOption::class, 'option_id');
    }

    public function assinge(){
        return $this->belongsTo(\App\User::class, 'user_id');
    }


}
