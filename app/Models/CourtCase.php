<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourtCase extends Model
{
    protected $guarded=[];
    protected $casts = [
        'witnesses' => 'array',
    ];
    public function client(){
        return $this->belongsTo(Client::class,'clientId');
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
        return $this->belongsTo(CourtList::class,'court');
    }
    public function caseDocument(){
        return $this->hasMany(CaseDocument::class,'courtCase_id');
    } 
    public function caseCategory(){
        return $this->belongsTo(CaseCategory::class,'case_category');
    }
}
