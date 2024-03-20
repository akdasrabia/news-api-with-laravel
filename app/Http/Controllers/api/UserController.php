<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Auth;
use App\Models\News;
use App\Models\User;
use App\Notifications\InvoicePaid;
use App\Notifications\NewsNotification;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{

    public function sendMail()
    {
        $user = User::first();
        $today = Carbon::today();
        $news = News::where('is_deleted', false)->whereDate('created_at', $today)->get(); 

        $user->notify(new NewsNotification($news));

        return response()->json([
            "status" => "fail",
            "message" => "You do not have permission to access this resource. Please log in again with correct credentials or sign up."
        ], 401);
    }



    public function userDetails()
    {
        if (Auth::check()) {

            $user = Auth::user();

            return response()->json([
                "status" => "success",
                "data" => $user
            ], 200);
        }

        return response()->json([
            "status" => "fail",
            "message" => "You do not have permission to access this resource. Please log in again with correct credentials or sign up."
        ], 401);
    }


    public function getUserNews()
    {
        if (Auth::check()) {

            $user = Auth::user();
            $news = News::where('is_deleted', 0)->where('user_id', $user->id)->get();

            return response()->json([
                "status" => "success",
                "news" => $news
            ], 200);



   
        }

        return response()->json([
            "status" => "fail",
            "message" => "You do not have permission to access this resource. Please log in again with correct credentials or sign up."
        ], 401);
    }




}
