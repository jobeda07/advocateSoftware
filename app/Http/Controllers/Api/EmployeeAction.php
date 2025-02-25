<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;

class EmployeeAction extends Controller
{  
    use ImageUpload;
    public function index(Request $request){
        try {
            $search=$request->query('search');
            $query=User::where('id','!=',1)->orderBy('id', 'DESC');

            if($search){
                $query->where(function ($q) use ($search){
                    $q->where("name","like","%{$search}%")
                       ->orWhere("phone","like","%{$search}%")
                       ->orWhere("email","like","%{$search}%")
                       ->orWhere("expertise_in","like","%{$search}%");
                });
             }
            $employeeData = $query->paginate(50)->appends($request->query());
            if ($employeeData->isEmpty()) {
                return response()->json(['data' => []], 404);
            }
            return EmployeeResource::collection($employeeData)
                ->additional(['status' => 200]);

        } catch (\Exception $e) {
            return response()->json([
                 'error' => 'Something went wrong: ' . $e->getMessage() ,
                 'status'=>500
            ]);
        }
    }
    
    public function store(EmployeeRequest $request){

        DB::beginTransaction();
       try{
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
                'portfolio_status' => 0,
                'password' => Hash::make('12345678'),
                'image' => $image,
                'designation' => $request->designation,
                'expertise_in' => $request->expertise_in,
                'address' => $request->address,
            ]);
            if ($request->roles) {
                $employeeData->assignRole($request->roles);
            }
            DB::commit();
            return response([
                'employee-data'=> new  EmployeeResource($employeeData),
                'message' => 'Data Created successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage() ,
                 'status'=>500
            ]);
        }
    } 
    public function update(Request $request,$id){
        $request->validate([
            'name' => 'required|string|max:150',
            'phone' =>['required',Rule::unique('users', 'phone')->ignore($id)],
            'email'=>'required|email|unique:users,email,'. $id,
            'join_date' => 'required',
            'designation' => 'required|string|max:150',
            'address' => 'required|string|max:180',
            'expertise_in' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'roles' => 'required|exists:roles,name',
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
                'portfolio_status' =>$employeeData->portfolio_status,
                'password' => $request->password ?? $employeeData->password,
                'image' => $image,
                'designation' => $request->designation,
                'expertise_in' => $request->expertise_in,
                'address' => $request->address,
            ]);
            if ($request->roles) {
                $employeeData->syncRoles($request->roles);
            }
            DB::commit();
            return response([
                'employee-data'=> new  EmployeeResource($employeeData),
                'message' => 'Data Update successfully'
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
            $employeeData=User::find($id);
            if(! $employeeData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $this->deleteOne($employeeData->image);
            $employeeData->delete();
            DB::commit();
            return response([
                'message' => ' Data Delete successfully'
            ]);
        }catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                 'error' => 'Something went wrong: ' . $e->getMessage() ,
                 'status'=>500
            ]);
        }
    }
    public function portfolio_status($id){
        DB::beginTransaction();
        try{
            $employeeData=User::find($id);
            if(! $employeeData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $employeeData->portfolio_status=$employeeData->portfolio_status == 1 ? 0 : 1;
            $employeeData->save();
            DB::commit();
            return response([
                'message' => ' Status Change successfully'
            ]);
        }catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage() ,
                 'status'=>500
            ]);
        }
    } 
    public function status($id){
        DB::beginTransaction();
        try{
            $employeeData=User::find($id);
            if(! $employeeData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $employeeData->status=$employeeData->status == 1 ? 0 : 1;
            $employeeData->save();
            DB::commit();
            return response([
                'message' => ' Status Change successfully'
            ]);
        }catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage() ,
                 'status'=>500
            ]);
        }
    }

    public function teamlist(){
        try {
            $employeeData = User::where('id','!=',1)->where('portfolio_status',1)->orderBy('id','desc')->paginate(50);

            return response()->json([
                'employee' =>EmployeeResource::collection($employeeData),
                 'status'=>200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                 'error' => 'Something went wrong: ' . $e->getMessage() ,
                 'status'=>500
            ]);
        }
    }
}
