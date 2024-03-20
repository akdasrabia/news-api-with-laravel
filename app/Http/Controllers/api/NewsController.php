<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NewsController extends Controller
{


    public function getAll()
    {
        $cachedNews = 0;
        
        // Cache'ten haber sayısını ve haberleri kontrol et
        if (  ($cachedNews = Cache::get('news') ) &&   ($cachedNewsCount = Cache::get('news_count'))) {
         

    
            // Cache'teki haber sayısı ile cache'teki haberlerin sayısı eşitse, sadece cache'teki veriyi döndür
            if (count($cachedNews) == $cachedNewsCount) {
                return response()->json([
                    "cache" => true,
                    "status" => "success",
                    'news' => $cachedNews
                ], 200);
            }
        }
    
        // Veritabanından tüm haberleri al
        $news = News::orderBy('created_at', 'desc')
            ->where('is_deleted', 'false')
            ->select('id', 'title', 'content', 'user_id', 'image', 'created_at', 'slug')
            ->with('user:id,name,image')
            ->get();
    
        // Verileri cache'e yaz ve cache'teki haber sayısını güncelle
        Cache::put('news', $news, now()->addMinutes(60)); 
        Cache::put('news_count', count($news), now()->addMinutes(60)); 
    
        return response()->json([
            "cache" => false,
            "status" => "success",
            'news' => $news
        ], 200);
    }

    public function getBySlug($slug)
    {
        $news = News::where('slug', $slug)
        ->where('is_deleted', 'false')
        ->select('id', 'title', 'content', 'user_id', 'image', 'is_deleted', 'slug', 'created_at')
        ->with([
            'user:id,name,image',
            'comments:id,user_id,comment',
            'comments.user:id,name,image'

        ])->first();
    
        if(! $news) {
            return response()->json([
                "status" => "fail",
                'message'=> "Not found",
            ], 404);
        }
    
        return response()->json([
            "status" => "success",
            'news'=> $news
        ], 200);
    }


    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'content' => 'required|string',
            'slug' => [
                'required',
                'string',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/i', // Slug formatını kontrol et
                function ($attribute, $value, $fail) {
                    // Slug'in benzersiz olup olmadığını kontrol et
                    if (\App\Models\News::where('slug', $value)->exists()) {
                        $fail('The slug has already been taken.');
                    }
                },
            ],
        ]);


        if ($validator->fails()) {
            return response()->json(["status" => "fail",'error' => $validator->errors()], 422);
        }

        $news = News::create([
            'content' => $request['content'],
            'title' => $request['title'],
            'slug' => $request['slug'],
            'user_id'=> auth()->user()->id
        ]);

        if (Cache::has('news_count')){
            $cachedNewsCount = Cache::get('news_count');
            $cachedNewsCount = $cachedNewsCount +1;
            Cache::put('news_count', $cachedNewsCount, now()->addMinutes(60)); 
        }

        return response()->json([
            "status" => "success",
            'message'=> "News successfully created",
             'news'=> $news
        ], 201);
    }



    public function updateBySlug(Request $request,string $slug)
    {
        $validator = Validator::make($request->all(), [
            'title'=> 'required|string',
            'content'=> 'required|string',
        ]);


        if ($validator->fails()) {
            return response()->json(["status" => "fail",'error' => $validator->errors()], 422);
        }
        $news = News::where('slug', $slug)->where('is_deleted', 'false')->first();

        if($news->user_id != auth()->user()->id){
            return response()->json([
                "status" => "fail",
                "message" => "You do not have permission to access this news.",
            ], 403);
        }
        if(!$news) {
            return response()->json([
                "status" => "fail",
                'message'=> "Not found"
            ], 404);
        }


        $news->update([
            'content' => $request['content'],
            'title' => $request['title'],
        ]);
        
        return response()->json([
            "status" => "success",
            "message" => "News updated successfully.",
        ], 200);
    }



    public function deleteBySlug($slug)
    {
        if (Cache::has('news_count')){
            $cachedNewsCount = Cache::get('news_count');
            $cachedNewsCount = $cachedNewsCount  - 1;
            Cache::put('news_count', $cachedNewsCount, now()->addMinutes(60)); 
        }
 
        $news = News::where('slug', $slug)->first();
        if($news->user_id != auth()->user()->id){
            return response()->json([
                "status" => "fail",
                "message" => "You do not have permission to access this news.",
            ], 403);
        }
        if(!$news) {
            return response()->json([
                "status" => "fail",
                'message'=> "Not found",
            ], 404);
        }


        $news->update([
            'is_deleted' => true,

        ]);
        return response()->json([
            "status" => "success",
            "message" => "News deleted successfully.",
        ], 204);
    }
}
