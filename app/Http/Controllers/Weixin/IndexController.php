<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    protected $redis_weixin_access_token='str:weixin_access_token';//微信 access_token
    //接受事件推送
    public function wxEvent(){
        $data=file_put_contents("php://input");
        $log_str=date('Y-m-d H:i:s')."\n".$data."\n<<<<<<<";
        file_put_contents('logs/wx_event.log',$log_str,FILE_APPEND);
    }
    //获取AccessToken
}