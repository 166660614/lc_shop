@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <form action="/upload/pdf" method="post" enctype="multipart/form-data" >
                        {{csrf_field()}}
                        <div class="form-group">
                            <label for="exampleInputFile">上传作业</label>
                            <input type="file" name="pdf">
                            <p class="help-block">请上传PDF格式的文件</p>
                        </div>
                        <button type="submit" class="btn btn-success">上传</button>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
