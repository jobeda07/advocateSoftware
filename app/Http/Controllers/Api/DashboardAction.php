<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;

class DashboardAction extends Controller
{
    public function dashboard(){
        try{
            
            $visitor_total=Visitor::count();
            $visitor=Visitor::where('created_at',today())->count();
            
            return response()->json([
                 'visitor_total' =>$visitor_total,
                 'visitor' =>$visitor,
                 'status'=>200

                 ]);
            
        }catch (\Exception $e) {
            return response()->json([
                'error' =>'Somethink Went Wrong',
                 'status'=>500
            ]);
        }
    }
}
