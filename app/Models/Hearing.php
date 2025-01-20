<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hearing extends Model
{
    protected $guarded=[];
    
    public function createdBy(){
        return $this->belongsTo(User::class,'created_by');
    }
    public function caseOf(){
        return $this->belongsTo(CourtCase::class,'caseId','caseId');
    }
    public function courtOf(){
        return $this->belongsTo(CourtList::class,'court_id');
    }
}
