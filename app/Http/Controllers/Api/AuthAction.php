<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\LoginResource;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthAction extends Controller
{
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

}