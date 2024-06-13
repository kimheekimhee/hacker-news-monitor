<?php

namespace App\Http\Controllers;

use App\Models\HackerNews;
use Illuminate\Http\Request;

class HackerNewsController extends Controller
{
    public function index()
    {
        $articles = HackerNews::latest()->take(10)->get();
        return view('hackernews.index', compact('articles'));
    }

    public function fetch()
    {
        $ids = file_get_contents('https://hacker-news.firebaseio.com/v0/topstories.json');
        $ids = json_decode($ids);

        $articles = [];
        foreach (array_slice($ids, 0, 10) as $id) {
            $item = file_get_contents("https://hacker-news.firebaseio.com/v0/item/$id.json");
            $item = json_decode($item);

            $articles[] = [
                'title' => $item->title,
                'url' => $item->url ?? '#'
            ];
        }

        HackerNews::truncate();
        HackerNews::insert($articles);

        return response()->json(['status' => 'success']);
    }
}
