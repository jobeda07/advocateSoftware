<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Traits\ImageUpload;

class EmployeeAction extends Controller
{  
    use ImageUpload;
    public function index(){
        try {
            $employee = User::where('id','!=',1)->orderBy('id','desc')->get();
            $employeeData = [];

            foreach ($employee as $item) {
                $employeeData[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'phone' => $item->phone,
                    'email' => $item->email ?? '',
                    'join_date' => $item->join_date,
                    'status' => $item->status == 1 ? 'active' : 'inactive',
                    'designation' => $item->designation,
                    'address' => $item->address,
                    'image' =>$item->image ? env('APP_URL') . "/" .$item->image : '',
                ];
            }
            return response()->json([
                'employee' =>$employeeData,
                 'status'=>200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>'data not found',
                 'status'=>500
            ]);
        }
    }
    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:150',
            'phone' =>['required', 'regex:/(\+){0,1}(88){0,1}01(3|4|5|6|7|8|9)(\d){8}/', 'digits:11','unique:users,phone'],
            'email'=>'nullable|email|unique:users,email',
            'join_date' => 'required',
            'designation' => 'required|string|max:150',
            'address' => 'required|string|max:180',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        DB::beginTransaction();
        // try{
            $image='';
            if (isset($request->image)) {
                $file = $request->image;
                $filename = $this->imageUpload($file, 300, 300, 'uploads/images/Employee/', true);
                $image = 'uploads/images/Employee/' . $filename;
            }

            $employeeData=User::create([
                'name'=>ucfirst($request->name),
                'phone' => $request->phone,
                'email' => $request->email ?? '',
                'join_date' => $request->join_date,
                'status' => 1,
                'password' => Hash::make('12345678'),
                'image' => $image,
                'designation' => $request->designation,
                'address' => $request->address,
            ]);
            DB::commit();
            return response([
                'employee-data'=> $employeeData,
                'message' => 'Data Created successfully'
            ]);
        // } catch (\Exception $e) {
        //     DB::rollback();
        //     return response()->json([
        //         'error' =>'Somethink went wrong',
        //          'status'=>500
        //     ]);
        // }
    } 
    public function update(Request $request,$id){
        $request->validate([
            'name' => 'required|string|max:150',
            'phone' =>['required', 'regex:/(\+){0,1}(88){0,1}01(3|4|5|6|7|8|9)(\d){8}/', 'digits:11',Rule::unique('users', 'phone')->ignore($id)],
            'email'=>'nullable|email|unique:users,email,'. $id,
            'join_date' => 'required',
            'designation' => 'required|string|max:150',
            'address' => 'required|string|max:180',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        DB::beginTransaction();
        try{

            $employeeData=User::find($id);
            if(! $employeeData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            if (isset($request->image)) {
                $this->deleteOne($employeeData->image);
                $file = $request->image;
                $filename = $this->imageUpload($file, 300, 300, 'uploads/images/Employee/', true);
                $image = 'uploads/images/Employee/' . $filename;
            }else{
                $image= $employeeData->image ?? '';
            }
            $employeeData->update([
                'name'=>ucfirst($request->name),
                'phone' => $request->phone,
                'email' => $request->email ?? '',
                'join_date' => $request->join_date,
                'status' =>$employeeData->status,
                'password' => $request->password ?? $employeeData->password,
                'image' => $image,
                'designation' => $request->designation,
                'address' => $request->address,
            ]);
            DB::commit();
            return response([
                'employee-data'=> $employeeData,
                'message' => 'Data Update successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' =>'Somethink went wrong',
                 'status'=>500
            ]);
        }
    } 

    public function delete($id){
        DB::beginTransaction();
        try{
            $employeeData=User::find($id);
            if($employeeData){
                $this->deleteOne($employeeData->image);
                $employeeData->delete();
            }
            DB::commit();
            return response([
                'message' => ' Data Delete successfully'
            ]);
        }catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' =>'Somethink Went Wrong',
                 'status'=>500
            ]);
        }
    }
}
