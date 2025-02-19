<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\LoginResource;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Models\Role;

class AuthAction extends Controller
{
    use ImageUpload;
    public function login(Request $request)
    {
        $credentials = $request->only('password');

        if (strlen($request->email_phone) == 11 && is_numeric($request->email_phone)) {
            $credentials['phone'] = $request->email_phone;
        } else {
            $credentials['email'] = $request->email_phone;
        }

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('customer')->plainTextToken;
            return response()->json([
                'token' => $token,
                'response_data' => new LoginResource($user),
                'message' => 'Login Successfully!',
                'status'  =>200
            ]);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }
    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();
        if ($token) {
            $token->delete();
            return response()->json(['message' => 'Successfully logged out'], 200);
        }
        return response()->json(['message' => 'Invalid token'], 401);
    }

    // function profile_update(Request $request,$id)
    // {
    //     try {
    //         $validatedData = $request->validated();
    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         return $this->sendError('Validation Error.', $e->errors());
    //     }
    //     $user = Customer::find($id);


    //     if (!$user) {
    //         return $this->sendError('User not found.');
    //     }
    //     $user->update($validatedData);
    //     if ($request->hasFile('image')) {
    //         $filename = $this->uploadOne($request->image, 500, 500, config('imagepath.user'));
    //         $this->deleteOne(config('imagepath.user'), $user->image);
    //         $user->update(['image' => $filename]);
    //     }
    //     if ($request->filled('password')) {
    //         $user->password = bcrypt($request->input('password'));
    //         $user->save();
    //     }
    //     return $this->sendResponse($user, 'User updated successfully.');
    // }


    public function getProfile()
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = auth()->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'designation' => $user->designation,
            'expertise_in' => $user->expertise_in,
            'status' => $user->status == 1 ? 'active' : 'inactive',
            'portfolio_status' => $user->portfolio_status == 1 ? 'active' : 'inactive',

            // Convert collection to array before using implode
            'role_id' => is_object($user) && method_exists($user, 'getRoleIds')
                ? implode(', ', $user->getRoleIds()->toArray())
                : '',

            'role_name' => is_object($user) && method_exists($user, 'getRoleNames')
                ? implode(', ', $user->getRoleNames()->toArray())
                : '',

            'joining_date' => Carbon::parse($user->join_date)->format('d-m-Y'),
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'image' => $user->image ?? '',
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);


    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        if (isset($request->image)) {
            $this->deleteOne($user->image);
            $file = $request->image;
            $filename = $this->imageUpload($file, 300, 300, 'uploads/images/Employee/', true);
            $image = 'uploads/images/Employee/' . $filename;
        }else{
            $image= $user->image ?? '';
        }

        $user->update([
            'name'=>ucfirst($request->name),
            'phone' => $request->phone,
            'email' => $request->email ?? '',
            'join_date' => $request->join_date,
            'status' =>$user->status,
            'portfolio_status' =>$user->portfolio_status,
            'image' => $image,
            'designation' => $request->designation,
            'expertise_in' => $request->expertise_in,
            'address' => $request->address,
        ]);

        // $user->syncRoles($request->roles);



        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'designation' => $user->designation,
            'expertise_in' => $user->expertise_in,
            'status' => $user->status == 1 ? 'active' : 'inactive',
            'portfolio_status' => $user->portfolio_status == 1 ? 'active' : 'inactive',
            'role_id' => is_object($user) && method_exists($user, 'getRoleIds')
                ? implode(', ', $user->getRoleIds()->toArray())
                : '',
            'role_name' => is_object($user) && method_exists($user, 'getRoleNames')
                ? implode(', ', $user->getRoleNames()->toArray())
                : '',
            'joining_date' => Carbon::parse($user->join_date)->format('d-m-Y'),
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'image' => $user->image ?? '',
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }


    public function updatePassword(Request $request)
    {
        try {
            if ($request->isMethod('post')) {
                $user = Auth::user();
                if ($user) {
                    $validator = Validator::make($request->all(), [
                        'current_password' => 'required',
                        'new_password' => 'required|min:6|max:30',
                        'confirm_password' => 'required|same:new_password',
                    ]);

                    if ($validator->fails()) {
                        return response()->json([
                            'errors' => $validator->errors(),
                        ], 400);
                    }

                    if (Hash::check($request->current_password, $user->password)) {
                        $user = User::where('id', $user->id)->first();
                        $user->password = Hash::make($request->new_password);
                        $user->save();

                        return response()->json([
                            'status' => true,
                            'message' => 'Your Password Updated Successfully',
                        ], 200);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Current Password does not match',
                        ], 405);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unauthorized',
                    ], 401);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Token',
                ], 405);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }



}
