@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <ul class="nav nav-pills">
                    <li role="presentation" class="active"><a href=@php($_SERVER['REQUEST_URI'])/home>首页</a></li>
                    <li role="presentation" class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            菜单管理 <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="/weixin/create_menus">创建菜单</a></li>
                            <li><a href="/weixin/del_menus">删除菜单</a></li>
                            <li><a href="/weixin/send_all_content">群发</a></li>
                        </ul>
                    </li>
                    <li role="presentation" class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            群发管理 <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="/weixin/send_all_content">群发文本</a></li>
                        </ul>
                    </li>
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
