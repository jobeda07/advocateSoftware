<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomeResource;
use App\Models\Home;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use App\Http\Requests\HomeRequest;
use Illuminate\Support\Facades\DB;


class HomeAction extends Controller
{
    use ImageUpload;

    public function show(){
         try{
             $homeData = Home::first();
             if(!isset($homeData)){
                 return response()->json([
                     'status'=>500,
                     'message' =>'Data not found',
                 ], 404);
             }
             return response([
                 'home-data'=> new HomeResource($homeData),
                 'status' => 200
             ]);
         } catch (\Exception $e) {
             return response()->json([
                 'error' => 'Something went wrong: ' . $e->getMessage() ,
                 'status'=>500
             ]);
         }
     }

    public function update(HomeRequest $request){
        DB::beginTransaction();
        try{
            $homeData = Home::first();
            if(!isset($homeData)){
                return response()->json([
                    'status'=>false,
                    'message' =>'Data not found',
                ], 404);
            }
            if (isset($request->image)) {
                $this->deleteOne($homeData->image);
                $file = $request->image;
                $filename = $this->imageUpload($file, 1000, 1000, 'uploads/images/homeImage/', true);
                $homeData->image = 'uploads/images/homeImage/' . $filename;
            }else{
                $homeData->image= $homeData->image;
            }
            $homeData->name = $request->name;
            $homeData->quote = $request->quote;
            $homeData->details = $request->details;
            $homeData->save();
            DB::commit();
            return response([
                'home-data'=> new HomeResource($homeData),
                'message' => 'Data Updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage() ,
                'status'=>500
            ]);
        }
    }

}
