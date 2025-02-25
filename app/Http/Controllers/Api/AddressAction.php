<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Division;
use App\Models\District;
use App\Models\Thana;

class AddressAction extends Controller
{
    public function division(){
        try {
            $division = Division::orderBy('id','desc')->get();
            $divisionData = [];

            foreach ($division as $item) {
                $divisionData[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'bn_name' => $item->bn_name,
                ];
            }
            return response()->json([
                'division' =>$divisionData,
                 'status'=>200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage(),
                 'status'=>500
            ]);
        }
    }
    public function district($id){
        try {
            $district = District::where('division_id',$id)->orderBy('id','desc')->get();
            $districtData = [];

            foreach ($district as $item) {
                $districtData[] = [
                    'id' => $item->id,
                    'division_id' => $item->division_id,
                    'name' => $item->name,
                    'bn_name' => $item->bn_name,
                ];
            }
            return response()->json([
                'district' =>$districtData,
                 'status'=>200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage(),
                 'status'=>500
            ]);
        }
    }
    public function thana($id){
        try {
            $thana = Thana::where('district_id',$id)->orderBy('id','desc')->get();
            $thanaData = [];

            foreach ($thana as $item) {
                $thanaData[] = [
                    'id' => $item->id,
                    'district_id' => $item->district_id,
                    'name' => $item->name,
                    'bn_name' => $item->bn_name,
                ];
            }
            return response()->json([
                 'thana' =>$thanaData,
                 'status'=>200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage(),
                 'status'=>500
            ]);
        }
    }
}
