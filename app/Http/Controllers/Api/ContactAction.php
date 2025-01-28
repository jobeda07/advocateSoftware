<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use Illuminate\Support\Facades\DB;

class ContactAction extends Controller
{
    use ImageUpload;

    public function update(contactRequest $request){
        DB::beginTransaction();
        try{
            $contactData = Contact::first();
            if(!isset($contactData)){
                return response()->json([
                    'status'=>false,
                    'message' =>'Data not found',
                ], 404);
            }
            if (isset($request->image)) {
                $this->deleteOne($contactData->image);
                $file = $request->image;
                $filename = $this->imageUpload($file, 1000, 1000, 'uploads/images/contactImage/', true);
                $contactData->image = 'uploads/images/contactImage/' . $filename;
            }else{
                $contactData->image= $contactData->image;
            }

            $contactData->latitude = $request->latitude;
            $contactData->longitude = $request->longitude;
            $contactData->address = $request->address;
            $contactData->email = $request->email;
            $contactData->phone = $request->phone;
            $contactData->facebook_link = $request->facebook_link;
            $contactData->location_details = $request->location_details;
            $contactData->save();
            DB::commit();
            return response([
                'contact-data'=> new ContactResource($contactData),
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
