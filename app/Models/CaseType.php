<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseType extends Model
{
    protected $guarded=[];

    public function case_category(){
       return $this->belongsTo(CaseCategory::class,'case_category_id');
    }
}
