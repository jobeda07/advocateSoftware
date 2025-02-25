<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use App\Http\Requests\ServiceRequest;
use Illuminate\Support\Facades\DB;

class ServiceAction extends Controller
{
    use ImageUpload;
    public function index(){
        try {

            $service = Service::orderBy('serial','asc')->paginate(50);
            return response()->json(['service_data' => ServiceResource::collection($service) ,'status'=>200]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage() ,
                'status'=>500
            ]);
        }
    }
    public function store(ServiceRequest $request)
    {
        DB::beginTransaction();
        try {
            $serviceData = Service::create([
                'title' => $request->title,
                'serial' => $request->serial,
                'description' => $request->description,
            ]);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = $this->imageUpload($file, 1000, 1000, 'uploads/images/serviceImage/', true);
                $serviceData->image = 'uploads/images/serviceImage/' . $filename;
            }

            if ($request->hasFile('icon')) {
                $file = $request->file('icon');
                $filename = $this->imageUpload($file, 1000, 1000, 'uploads/images/serviceIcon/', true);
                $serviceData->icon = 'uploads/images/serviceIcon/' . $filename;
            }

            $serviceData->save();

            DB::commit();

            return response()->json([
                'service-data' => new ServiceResource($serviceData),
                'message' => 'Data created successfully',
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage() ,
                'status' => 500,
            ]);
        }
    }


    public function update(ServiceRequest $request,$id){

        DB::beginTransaction();
        try{
            $serviceData = Service::find($id);
            if(!isset($serviceData)){
                return response()->json([
                    'status'=>false,
                    'message' =>'Data not found',
                ], 404);
            }
            if (isset($request->image)) {
                $this->deleteOne($serviceData->image);
                $file = $request->image;
                $filename = $this->imageUpload($file, 1000, 1000, 'uploads/images/serviceImage/', true);
                $serviceData->image = 'uploads/images/serviceImage/' . $filename;
            }else{
                $serviceData->image= $serviceData->image;
            }

            if (isset($request->icon)) {
                $this->deleteOne($serviceData->icon);
                $file = $request->icon;
                $filename = $this->imageUpload($file, 1000, 1000, 'uploads/images/serviceIcon/', true);
                $serviceData->icon = 'uploads/images/serviceIcon/' . $filename;
            }else{
                $serviceData->icon= $serviceData->icon;
            }

            $serviceData->title = $request->title;
            $serviceData->serial = $request->serial;
            $serviceData->description = $request->description;
            $serviceData ->save();
            DB::commit();
            return response([
                'service-data'=> new ServiceResource($serviceData),
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

    public function delete($id){
        DB::beginTransaction();
        try{
            $serviceData = Service::find($id);
            if (!isset($serviceData))
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Not Found',
                ], 404);
            }
            if($serviceData){
                $this->deleteOne($serviceData->image);
                $this->deleteOne($serviceData->icon);
                $serviceData->delete();
            }

            DB::commit();
            return response([
                'message' => ' Data Deleted successfully'
            ]);
        }catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage() ,
                'status'=>500
            ]);
        }
    }
}
