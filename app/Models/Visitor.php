<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $guarded=[];

    public function casetype(){
        return $this->belongsTo(CaseType::class,'case_type');
    }
    public function createdBy(){
        return $this->belongsTo(User::class,'created_by');
    }
}
