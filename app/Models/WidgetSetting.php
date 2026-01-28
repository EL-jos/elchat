<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WidgetSetting extends BaseModel
{
    protected $table = 'widget_settings';

    public function site(){
        return $this->belongsTo(Site::class);
    }
}
