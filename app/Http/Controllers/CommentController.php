<?php

namespace App\Http\Controllers;
use Validator;
use JWTAuth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use App\Models\Post;
use App\Models\Comment;

class CommentController extends Controller
{
    //create comment-any logged In user can create commets
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
        'postId' => 'required|numeric',
        'body' => 'required|string'
       ]);  

        if ($validator->fails()) {  
            return response()->json(['error'=>$validator->errors()], 401); 
        }  
        $user = JWTAuth::authenticate($token); 
        $post = Post::where('id','=',$request->postId)->first();
        if(!$post){
            return response()->json([
                'success' => false,
                'message' => 'No such Post found'
            ], 200);
        } 
        $comment = new Comment();
        $comment->body = $request->body;
        $comment->post_id = $post->id;
        $comment->save();
        return response()->json(['comment' => $comment]);
    }
    //get all comments on a Post--writer can read comments on their Posts only
    public function getAll(Request $request)
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
        'postId' => 'required|numeric'
            ]);  

        if ($validator->fails()) {  
            return response()->json(['error'=>$validator->errors()], 401);
        }
        $user = JWTAuth::authenticate($token); 
        $profile = Profile::where('user_id','=',$user->id)->first();
        $post = Post::where('id','=',$request->postId)->first();
        if(!$profile){
            return response()->json([
                'success' => false,
                'message' => 'User does not have any profile created'
            ], 200);
        } 
        if(!$post){
            return response()->json([
                'success' => false,
                'message' => 'No Such Post Found'
            ], 401);
        }
        if($profile->status == 'writer'){
            if($post->user_id != $user->id){
                return response()->json([
                    'success' => false,
                    'message' => 'Post does nor belongs to this writer'
                ], 401);
            }
        }
        return response()->json(['comments' => $post->comments()->get()]);
    }
}