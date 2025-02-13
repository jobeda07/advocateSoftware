<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseHistory extends Model
{
    protected $guarded=[''];
    public function createdBy(){
        return $this->belongsTo(User::class,'created_by');
    }
    public function caseOf(){
        return $this->belongsTo(CourtCase::class,'caseId','caseId');
    }
}
