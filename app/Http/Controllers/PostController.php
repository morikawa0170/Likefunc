<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Post; //このcontrollerでPostモデルを使う
use Auth; 

class PostController extends Controller
{
    public function __construct() //ミドルウェアをこのコントローラーに付与。
    {
        $this->middleware('auth'); //ログインしていないユーザーはログイン画面に飛ばされる
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all();
        $posts->load('user');
        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $post = new Post; //インスタンスを作成
        $post -> title    = $request -> title; //ユーザー入力のtitleを代入
        $post -> body     = $request -> body; //ユーザー入力のbodyを代入
        $post -> user_id  = Auth::id(); //ログイン中のユーザーidをuser_idカラムに代入

        $post -> save(); //保存
        
        return redirect()->route('posts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);

        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id);
        if(Auth::id() !== $post->user_id){ //Auth::id()　→ログイン中ユーザーのid, $post->user_id → 該当の投稿のuser_id
            return abort(404);
        }
        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, $id)
    {
        $post = Post::find($id);
        
        if(Auth::id() !== $post->user_id){
            return abort(404);
        }

        $post -> title    = $request -> title;
        $post -> body     = $request -> body;
        $post -> save();

        return view('posts.show', compact('post'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        if(Auth::id() !== $post->user_id){
            return abort(404);
        }
        $post -> delete();

        return redirect()->route('posts.index');
    }
}
