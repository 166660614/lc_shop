@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <ul class="nav nav-pills">
                    <li role="presentation" class="active"><a href=@php($_SERVER['REQUEST_URI'])/home>首页</a></li>
                    <li role="presentation"><a href="/weixin/create_menus">菜单管理</a></li>
                </ul>
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    您已登录!
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
