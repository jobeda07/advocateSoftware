<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestimonialResource;
use App\Models\Testimonial;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use App\Http\Requests\TestimonialRequest;
use Illuminate\Support\Facades\DB;

class TestimonialAction extends Controller
{
    use ImageUpload;
    public function index(){
        try {

            $testimonial = Testimonial::orderBy('id','desc')->paginate(50);
            return response()->json(['testimonial_data' => TestimonialResource::collection($testimonial) ,'status'=>200]);

        } catch (\Exception $e) {
            return response()->json([
                'error' =>'data not found',
                'status'=>500
            ]);
        }
    }
    public function store(TestimonialRequest $request)
    {
        DB::beginTransaction();
        try {
            $testimonialData = Testimonial::create([
                'name' => $request->name,
                'profession' => $request->profession,
                'quote' => $request->quote,
            ]);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = $this->imageUpload($file, 1000, 1000, 'uploads/images/testimonialImage/', true);
                $testimonialData->image = 'uploads/images/testimonialImage/' . $filename;
            }

            $testimonialData->save();

            DB::commit();

            return response()->json([
                'testimonial-data' => new TestimonialResource($testimonialData),
                'message' => 'Data created successfully',
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Testimonial Store Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage(),
                'status' => 500,
            ]);
        }
    }


    public function update(TestimonialRequest $request, $id){

        DB::beginTransaction();
        try{
            $testimonialData = Testimonial::find($id);
            //dd($serviceData);
            if(!isset($testimonialData)){
                return response()->json([
                    'status'=>false,
                    'message' =>'Data not found',
                ], 404);
            }
            if (isset($request->image)) {
                $this->deleteOne($testimonialData->image);
                $file = $request->image;
                $filename = $this->imageUpload($file, 1000, 1000, 'uploads/images/testimonialImage/', true);
                $testimonialData->image = 'uploads/images/testimonialImage/' . $filename;
            }else{
                $testimonialData->image= $testimonialData->image;
            }


            $testimonialData->name = $request->name;
            $testimonialData->profession = $request->profession;
            $testimonialData->quote = $request->quote;
            $testimonialData ->save();
            DB::commit();
            return response([
                'testimonial-data'=> new TestimonialResource($testimonialData),
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

    public function delete($id){
        DB::beginTransaction();
        try{
            $testimonial = Testimonial::find($id);
            if (!isset($testimonial)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Not Found',
                ], 404);
            }
            if($testimonial){
                $this->deleteOne($testimonial->image);
                $testimonial->delete();
            }

            DB::commit();
            return response([
                'message' => ' Data Deleted successfully'
            ]);
        }catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' =>'Something Went Wrong',
                'status'=>500
            ]);
        }
    }
}
