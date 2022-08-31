<?php
 
namespace App\Http\Controllers;
 
use JWTAuth;
use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public $token = true;
  
    public function register(Request $request)
    {
 
        $validator = Validator::make($request->all(), 
                      [ 
                      'phone' => 'required',
                      'email' => 'required|email',
                      'password' => 'required',  
                      'confirm_password' => 'required|same:password', 
                     ]);  
 
        if ($validator->fails()) {  
            return response()->json(['error'=>$validator->errors()], 401); 
        }   
 
 
        $alreadyExist = User::where('email','=',$request->email)->get()->first();
        if($alreadyExist){
            {  
                return response()->json(['error'=>'email already Exist'], 401); 
            }   
        }
        $user = new User();
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
  
        if ($this->token) {
            return $this->login($request);
        }
  
        return response()->json([
            'success' => true,
            'data' => $user
        ], Response::HTTP_OK);
    }
  
    public function login(Request $request)
    {
        $input = $request->only('email', 'password');
        $jwt_token = null;
  
        if (!$jwt_token = JWTAuth::attempt($input)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ], Response::HTTP_UNAUTHORIZED);
        }
  
        return response()->json([
            'success' => true,
            'token' => $jwt_token,
        ]);
    }
  
    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        if(!$token){
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }
  
        try {
            JWTAuth::invalidate($token);
  
            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
  
    public function getUser(Request $request)
    {
        $token = $request->bearerToken();
        if(!$token){
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }
        $user = JWTAuth::authenticate($token); 
        return response()->json(['user' => $user]);
    }
}