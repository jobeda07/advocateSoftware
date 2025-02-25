<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
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

        if (is_numeric($request->email_phone)) {
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


    public function getSetting()
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }


        $siteData = SiteSetting::first();
        if(!isset($siteData)){
            return response()->json([
                'status'=>false,
                'message' =>'Data not found',
            ], 404);
        }

        return response()->json([
            'currency_symbol' => $siteData->currency_symbol,
            'currency_code' => $siteData->currency_code,
            'payment_method' => $siteData->payment_method,
            'selected_language' =>$siteData->selected_language,
            'site_logo' => $siteData->site_logo,
            'fav_icon' => $siteData->fav_icon,
            'created_at' => $siteData->created_at,
            'updated_at' => $siteData->updated_at,
        ]);
    }



    public function getSettingUpdate(Request $request)
    {
        $siteData = SiteSetting::first();

        if (!$siteData) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        if (isset($request->site_logo)) {
            $this->deleteOne($siteData->site_logo);
            $file = $request->site_logo;
            $filename = $this->imageUpload($file, 300, 300, 'uploads/images/site-setting/', true);
            $image = 'uploads/images/site-setting/' . $filename;
        }else{
            $image= $siteData->site_logo ?? '';
        }

        if (isset($request->fav_icon)) {
            $this->deleteOne($siteData->fav_icon);
            $file = $request->fav_icon;
            $filename = $this->imageUpload($file, 50, 50, 'uploads/images/site-setting/', true);
            $image2 = 'uploads/images/site-setting/' . $filename;
        }else{
            $image2= $siteData->fav_icon ?? '';
        }

        $siteData->update([
            'currency_symbol' => $request->currency_symbol,
            'currency_code' => $request->currency_code,
            'payment_method' => json_decode($request->input('payment_method'), true),
            'selected_language' =>$request->selected_language,
            'site_logo' => $image,
            'fav_icon' => $image2,
        ]);


        return response()->json([
            'currency_symbol' => $siteData->currency_symbol,
            'currency_code' => $siteData->currency_code,
            'payment_method' => $siteData->payment_method,
            'selected_language' =>$siteData->selected_language,
            'site_logo' => $siteData->site_logo,
            'fav_icon' => $siteData->fav_icon,
            'created_at' => $siteData->created_at,
            'updated_at' => $siteData->updated_at,
        ]);
    }



}
