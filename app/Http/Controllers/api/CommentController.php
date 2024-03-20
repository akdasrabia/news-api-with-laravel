<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function getCommentsByUserId()
    {
        $comments = Comment::orderBy('created_at', 'desc')
               ->where('user_id', 2)
               ->whereHas('news', function ($query) {
                   $query->where('is_deleted', false);
               })
               ->where('is_deleted', false)
               ->get();
        return response()->json([
            "status" => "success",
            'comments'=> $comments
        ], 200);
    }

    public function getCommentsByNewsId(int $id)
    {
        $comments = Comment::orderBy('created_at', 'desc')
               ->where('news_id', $id)
               ->whereHas('news', function ($query) {
                   $query->where('is_deleted', false);
               })
               ->where('is_deleted', false)
               ->get();
        return response()->json([
            "status" => "success",
            'comments'=> $comments
        ], 200);
    }



    public function createComment(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'comment'=> 'required|string',
        ]);

        $comment = Comment::create([
            'comment' => $request['comment'],
            'user_id'=> auth()->user()->id,
            'news_id'=> $id
        ]);

        return response()->json([
            "status" => "success",
            'message'=> "Comment successfully created",
            'comment'=> $comment
        ], 201);



    }


    public function deleteById($id)
    {
 
        $comment = Comment::findById($id);

        if($comment->user_id != auth()->user()->id){
            return response()->json([
                "status" => "fail",
                "message" => "You do not have permission to access this comment.",
            ], 403);
        }
        if(!$comment) {
            return response()->json([
                "status" => "fail",
                'message'=> "Not found"
            ], 404);
        }
        $comment->update([
            'is_deleted' => true,
        ]);
        return response()->json([
            "status" => "success",
            "message" => "Comment deleted successfully.",
        ], 204);
    }
}
