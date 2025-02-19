<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourtCase extends Model
{
    protected $guarded=[];
    protected $casts = [
        'witnesses' => 'array',
    ];
    public function clientAdd(){
        return $this->belongsTo(Client::class,'clientId','clientId');
    } 
    public function clientType(){
        return $this->belongsTo(ClientType::class,'client_type');
    } 
    public function caseType(){
        return $this->belongsTo(CaseType::class,'case_type');
    }
    public function caseSection(){
        return $this->belongsTo(CaseSection::class,'case_section');
    }

    public function caseStage(){
        return $this->belongsTo(CaseStage::class,'case_stage');
    }
    public function courtAdd(){
        return $this->belongsTo(CourtList::class,'court_id');
    }
    public function caseDocument(){
        return $this->hasMany(CaseDocument::class,'courtCase_id');
    } 
    public function caseCategory(){
        return $this->belongsTo(CaseCategory::class,'case_category');
    }
    public function caselawer(){
        return $this->belongsTo(User::class,'case_lawer_id');
    }
    public function createdBy(){
        return $this->belongsTo(User::class,'created_by');
    }
    
     public function case_fee(){
        return $this->hasMany(CaseFee::class,'caseId','caseId');
    }
}
