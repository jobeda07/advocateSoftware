<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use Spatie\Permission\Models\Permission;
use App\Http\Resources\PermissionResource;
use App\Http\Requests\RolePermissionRequest;


class PermissionAction extends Controller
{
    
    public function  index(){
        try{
            $roles = Role::where('guard_name', 'web')->where('id', '!=', 1)->orderBy('id', 'desc')->paginate(50);
            if(!isset($roles)){
                return response()->json([
                    'status'=>500,
                    'message' =>'Data not found',
                ], 404);
            }
            return response([
                'home-data'=>RoleResource::collection($roles),
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>'Something went wrong',
                'status'=>500
            ]);
        }
    }
    public function permission()
    {
        try{
            $permissions = Permission::where('guard_name', 'web')->get()->groupBy('module_name');
            if ($permissions->isEmpty()) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Data not found',
                ], 404);
            }

            return response()->json([
                'permissions-data' => $permissions->map(function ($group, $moduleName) {
                    return [
                        'module_name' => $moduleName,
                        'permissions' => PermissionResource::collection($group),
                    ];
                })->values(),
                'status' => 200
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' =>'Something went wrong',
                'status'=>500
            ]);
        }
    }

    public function store(RolePermissionRequest $request){
        try{
            DB::beginTransaction();
            $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
            $permissions = $request->permissions;
            if (!empty($permissions)) {
                $role->syncPermissions($permissions);
                DB::commit();
                return response([
                    'message' =>'Role Permission Create Successfully',
                    'status' => 200
                ]);
            }

        }catch(\Exception $e){
          DB::rollback();
            return response()->json([
                'error' =>'Something went wrong',
                'status'=>500
            ]);
        }
        
    }
 
    public function show($id)
    {
        try {
            $intid = intval($id);
            $role = Role::find($intid);

            if (!$role) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Data not found',
                ], 404);
            }
            if ($role->id ==1 ) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Permission Not Allow For SuperAdmin',
                ], 404);
            }

            $permissions = $role->permissions->groupBy('module_name'); // Group permissions by module_name

            return response()->json([
                'role' =>new RoleResource($role),
                'permissions-data' => $permissions->map(function ($group, $moduleName) {
                    return [
                        'module_name' => $moduleName,
                        'permissions' => PermissionResource::collection($group), // Now works correctly
                    ];
                })->values(), // Ensure response is a clean array
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>'Something went wrong',
                'status'=>500
            ]);
        }
    }

    public function update(Request $request, $id)
    { 
            $intId = intval($id);
            $request->validate([
                'name' => ['required', 'string', 'max:24', 'min:2',
                    Rule::unique('roles')->ignore($intId)->where('guard_name', 'web')
                ],
                'permissions' => ['required', 'array'],
                'permissions.*' => [
                    'string',
                    Rule::exists('permissions', 'name')->where('guard_name', 'web'),
                ],
            ]);
            try {
                $role = Role::find($intId);
                if (!$role) {
                    return response()->json([
                        'status' => 500,
                        'message' => 'Data not found',
                    ], 404);
                }
                if ($role->id ==1 ) {
                    return response()->json([
                        'status' => 500,
                        'message' => 'Permission Not Allow For SuperAdmin',
                    ], 404);
                }
                DB::beginTransaction();
                $permissions = $request->input('permissions');
                $role->syncPermissions($permissions);
                $role->name = $request->name;
                $role->update();
                DB::commit();
                return response([
                    'message' =>'Role Permission Update Successfully',
                    'status' => 200
                ]);
            }catch(\Exception $e){
                DB::rollback();
                return response()->json([
                    'error' =>'Something went wrong',
                    'status'=>500
                ]);
            }
    }
   
}
