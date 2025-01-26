<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToDoList extends Model
{
    protected $guarded = [];

    public function createBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
