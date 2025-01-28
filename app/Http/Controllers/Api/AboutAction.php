<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AboutResource;
use App\Models\About;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use App\Http\Requests\AboutRequest;
use Illuminate\Support\Facades\DB;

class AboutAction extends Controller
{
    use ImageUpload;

    public function show(){
        try{
            $aboutData = About::first();
            // dd($aboutData);
            if(!isset($aboutData)){
                return response()->json([
                    'status'=>false,
                    'message' =>'Data not found',
                ], 404);
            }
            return response([
                'about-data'=> new AboutResource($aboutData),
                'status'=>200
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' =>'Something went wrong',
                'status'=>500
            ]);
        }
    }
    public function update(AboutRequest $request){
        //dd('fg');
        DB::beginTransaction();
        try{
            $aboutData = About::first();
            // dd($aboutData);
            if(!isset($aboutData)){
                return response()->json([
                    'status'=>false,
                    'message' =>'Data not found',
                ], 404);
            }
            if (isset($request->image)) {
                $this->deleteOne($aboutData->image);
                $file = $request->image;
                $filename = $this->imageUpload($file, 1000, 1000, 'uploads/images/aboutImage/', true);
                $aboutData->image = 'uploads/images/aboutImage/' . $filename;
            }else{
                $aboutData->image= $aboutData->image;
            }
            $aboutData->title = $request->title;
            $aboutData->details = $request->details;
            $aboutData->save();
            DB::commit();
            return response([
                'about-data'=> new AboutResource($aboutData),
                'message' => 'Data Updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' =>'Something went wrong',
                'status'=>500
            ]);
        }
    }
}
