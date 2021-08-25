@extends('layouts.app')

@section('content')
<div class="container">
   <div class="row justify-content-center">
      <div class="col-md-8">
         @if ($errors->any())
               <div class="alert alert-danger">
                  <ul>
                     @foreach ($errors->all() as $error)
                           <li>{{ $error }}</li>
                     @endforeach
                  </ul>
               </div>
         @endif
         <form action="{{ route('posts.store') }}" method="POST">
         {{csrf_field()}}
               <div class="form-group">
                  <label>タイトル</label>
                  <input type="text" class="form-control" placeholder="タイトルを入力して下さい" name="title">
               </div>
               <div class="form-group">
                  <label>内容</label>
                  <textarea class="form-control" placeholder="内容" rows="5" name="body"></textarea>
               </div>
               <button type="submit" class="btn btn-primary">作成する</button>
         </form>
      </div>
   </div>
</div>
@endsection