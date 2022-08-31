<?php

namespace App\Http\Controllers;

use Validator;
use JWTAuth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;

class ProfileController extends Controller
{
    //create Profile
    public function create(Request $request)
    {
        $token = $request->bearerToken();
        if(!$token){
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }
        $validator = Validator::make($request->all(), 
        [ 
        'firstName' => 'required|string',
        'lastName' => 'required|string',
        'status' => 'required|string'
       ]);  

        if ($validator->fails()) {  
            return response()->json(['error'=>$validator->errors()], 401); 
        }  
        $user = JWTAuth::authenticate($token); 
        $profile = Profile::where('user_id','=',$user->id)->first();
        if($profile){
            return response()->json([
                'success' => false,
                'message' => 'User Allready has Profile details'
            ], 200);
        } 
        $profile = new Profile();
        $profile->firstName = $request->firstName;
        $profile->lastName = $request->lastName;
        $profile->status = $request->status;
        $profile->user_id = $user->id;
        $profile->save();
        return response()->json(['userProfile' => $profile]);
    }
    //update Profile
    public function update(Request $request)
    {
        $token = $request->bearerToken();
        if(!$token){
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }
        $validator = Validator::make($request->all(), 
        [ 
        'firstName' => 'required|string',
        'lastName' => 'required|string',
        'status' => 'required|string'
       ]);  

        if ($validator->fails()) {  
            return response()->json(['error'=>$validator->errors()], 401); 
        }  
        $user = JWTAuth::authenticate($token); 
        $profile = Profile::where('user_id','=',$user->id)->first();
        if(!$profile){
            return response()->json([
                'success' => false,
                'message' => 'User does not have any profile created'
            ], 200);
        } 
        $profile->firstName = $request->firstName;
        $profile->lastName = $request->lastName;
        $profile->status = $request->status;
        $profile->save();
        return response()->json(['userProfile' => $profile]);
    }
    //get Profile
    public function get(Request $request)
    {
        $token = $request->bearerToken();
        if(!$token){
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }
        $user = JWTAuth::authenticate($token); 
        $profile = Profile::where('user_id','=',$user->id)->first();
        if(!$profile){
            return response()->json([
                'success' => false,
                'message' => 'No Profile Found for this User'
            ], 200);
        } 
        return response()->json(['userProfile' => $profile]);
    }
}
