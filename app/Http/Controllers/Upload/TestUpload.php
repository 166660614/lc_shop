<?php

namespace App\Http\Controllers\Upload;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestUpload extends Controller
{
    public function upload(){
        return view('upload.pdf');
    }
    public function uploadpdf(Request $request){
        $file=$request->file('pdf');
        $ext=$file->extension();
        //print_r($ext);exit;
        if($ext!='pdf'){
            die('必须上传PDF格式文件');
        }
        $res=$file->storeAs(date('Ymd'),str_random('6').'.pdf');
        if($res){
            echo '上传成功';
        }
    }
}
