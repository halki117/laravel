<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Article;
use App\Tag;

use App\Http\Requests\ArticleRequest;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Article::class, 'article');
    }

    public function index()
    {
        $articles = Article::all()->sortByDesc('created_at');

        return view('articles.index')->with(['articles' => $articles]);
    }

    public function create()
    {
        $allTagNames = Tag::all()->map(function($tag){
            return ['text' => $tag->name];
        });
        return view('articles.create', [
            'allTagNames' => $allTagNames,
        ]);
    }

    public function store(ArticleRequest $request, Article $article)
    {
        $article->fill($request->all());
        $article->user_id = $request->user()->id;
        $article->save();

        $request->tags->each(function ($tagName) use ($article) {
            // タグが既に登録されているかを調べる。登録されていればそのモデルを、登録されていなければテーブルに保存の上モデルを返す
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            // 記事とタグの紐付け(article_tagテーブルへの登録)が行われる
            $article->tags()->attach($tag);
        });

        return redirect()->route('articles.index'); 
    }

    public function edit(Article $article)
    {
        // パスには article/{article}/edit $article (Ariticleのインスタンス)には、自動的にidが付与される。
        $tagNames = $article->tags->map(function($tag){
            return ['text'=> $tag->name];
        });

        $allTagNames = Tag::all()->map(function ($tag) {
            return ['text' => $tag->name];
        });

        return view('articles.edit',[
            'article' => $article,
            'tagNames' => $tagNames,
            'allTagNames' => $allTagNames,
        ]);
    }
    
    public function update( ArticleRequest $request ,Article $article)
    {
        $article->fill($request->all())->save();

        $article->tags()->detach();
        $request->tags->each(function($tagName) use ($article){
            $tag = Tag::firstOrCreate(['name' => $tagName ]);
            $article->tags()->attach($tag);
        });      
        return redirect()->route('articles.index');
    }
    
    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index');
    }
    
    public function show(Article $article)
    {
        return view('articles.show', ['article' => $article]);
    }

    public function like(Request $request, Article $article)
    {
        $article->likes()->detach($request->user()->id);
        $article->likes()->attach($request->user()->id);

        return[
            'id' => $article->id,
            'countLike' => $article->count_likes,
        ];
    }


    public function unlike(Request $request, Article $article)
    {
        $article->likes()->detach($request->user()->id);

        return[
            'id' => $article->id,
            'countLike' => $article->count_likes,
        ];
    }
    
}