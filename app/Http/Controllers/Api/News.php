<?php

namespace App\Http\Controllers\Api;

use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Rss_News;
class News extends Controller
{
    public function getNews(Request $request){
        $currentPage = $request->page ?? 1;
        $limit = $request->limit ?? 20;
        $newsList = Rss_News::select('name', 'shortDescription', 'pubDate', 'author', 'image');
        $countPage = $newsList->count();
        if($countPage / $limit < $currentPage) {
            return response()->json([
                'error' => 'Передан некорректный параметр `page`'
            ], 400);
        }
        if($limit > 100 ) {
            return response()->json([
                'error' => 'Передан некорректный параметр `limit`'
            ], 400);
        }
        $newsList->orderBy('pubDate', $request->sort ?? 'DESC')
                    ->skip(($currentPage-1) * $limit)
                    ->take($limit);

        $data = [
            'page' => $currentPage,
            'limit' => $limit,
            'news' => $newsList->get(),
        ];
        if(count($data['news']) == 0) {
            return response()->json([
                'error'=>'Новостей по заданному фильтру не найдено'
            ], 200);
        }

        return response()->json($data, 200);
    }
}
