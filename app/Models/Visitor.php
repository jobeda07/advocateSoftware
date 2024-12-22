<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $guarded=[];
    public function case_type(){
        return $this->belongsTo(CaseType::class,'case_type');
    }
}
