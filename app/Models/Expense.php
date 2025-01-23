<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $guarded=[];

    public function expense_category(){
        return $this->belongsTo(ExpenseCategory::class,'expense_category_id');
    }
    public function createdBy(){
         return $this->belongsTo(User::class,'created_by');
    }

}
