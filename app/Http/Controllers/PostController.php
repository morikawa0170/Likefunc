<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\DB;
use App\Post; //このcontrollerでPostモデルを使う
use App\User;
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
    public function show($id, Request $request) //$id == post_id
    {
        $mypost = null;
        $match_user_id = null;
        $matching = null;
        
        $post = Post::find($id);
        //post_idと$idが一致しているものを取得
        if(Auth::id()==$post->user_id){
            $mypost = true;
        }

        //post_idが$idと同じレコードの中で、user_idが2つあるものを取得
        $count = DB::select("select * from post_user
                        where post_id = $id
                        group by user_id
                        having count(*)>1");

        
        if(isset($count)){
            foreach($count as $item){
                //重複しているuser_idを取得
                $match_user_id = $item->user_id;
            }
            $matching = User::find($match_user_id);
        }

        return view('posts.show', [
            'post' => $post,
            'mypost' => $mypost,
            'count' => $count,
            'matching' => $matching
        ]);
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
