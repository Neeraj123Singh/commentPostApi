<?php

namespace App\Http\Controllers;

use Validator;
use JWTAuth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use App\Models\Post;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    //create Post
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
        'title' => 'required|string',
        'body' => 'required|string'
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
        if($profile->status != 'writer'){
            return response()->json([
                'success' => false,
                'message' => 'User does not have role to create Post'
            ], 200);
        }
        $post = new Post();
        $post->title = $request->title;
        $post->body = $request->body;
        $post->user_id = $user->id;
        $post->save();
        return response()->json(['userPost' => $post]);
    }
    //update post
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
        'title' => 'required|string',
        'body' => 'required|string',
        'id' => 'required|numeric'
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
        if($profile->status != 'writer'){
            return response()->json([
                'success' => false,
                'message' => 'User does not have role to update Post'
            ], 200);
        }
        $post = Post::where('id','=',$request->id)->first();
        if(!$post){
            return response()->json([
                'success' => false,
                'message' => 'User does not have any post created'
            ], 200);
        } 
        if($post->user_id != $user->id){
            return response()->json([
                'success' => false,
                'message' => 'Post does not belongs to this writer.'
            ], 200);
        }
        $post->title = $request->title;
        $post->body = $request->body;
        $post->save();
        return response()->json(['userPost' => $post]);
    }
    //get post
    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), 
        [ 
        'id' => 'required|numeric'
         ]);  

        if ($validator->fails()) {  
            return response()->json(['error'=>$validator->errors()], 401); 
        }  
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
                'message' => 'User does not have any profile created'
            ], 200);
        } 
        if($profile->status == 'writer'){
            $post = Post::where('id','=',$request->id)->first();
            if(!$post){
                return response()->json([
                    'success' => false,
                    'message' => 'No such post Found for this User'
                ], 200);
            } 
            if($post->user_id != $user->id){
                return response()->json([
                    'success' => false,
                    'message' => 'Post does not belongs to this writer.'
                ], 200);
            }
            return response()->json(['userpost' => $post]);
        }else{
            $post = Post::where('id','=',$request->id)->first();
            if(!$post){
                return response()->json([
                    'success' => false,
                    'message' => 'No such post Found'
                ], 200);
            } 
             return response()->json(['userpost' => $post]);
        }
    }
    //get All Post
    public function getAll(Request $request)
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
                'message' => 'User does not have any profile created'
            ], 200);
        } 
        if($profile->status == 'writer'){
            $posts = Post::where('user_id','=',$user->id)->get();
            return response()->json(['userpost' => $posts]);
        }else{
            return response()->json(['userpost' => Post::get()]);
        }
    }
    //delete Post
    public function delete(Request $request)
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
        'id' => 'required|numeric'
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
        if($profile->status != 'writer'){
            return response()->json([
                'success' => false,
                'message' => 'User does not have role to delete Post'
            ], 200);
        }
        $post = Post::where('id','=',$request->id)->first();
        if(!$post){
            return response()->json([
                'success' => false,
                'message' => 'User does not have any such post created'
            ], 200);
        }
        if($post->user_id != $user->id){
            return response()->json([
                'success' => false,
                'message' => 'Post does not belongs to this writer.'
            ], 200);
        } 
        $post->delete();
        return response()->json(['success' => true]);
    }
}
