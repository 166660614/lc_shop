<?php

namespace App\Http\Controllers\Weixin;

use App\Model\WxModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class IndexController extends Controller
{
    protected $redis_weixin_access_token='astr:weixin_access_token';//微信 access_token
    //接受微信服务器事件推送
    public function wxEvent(){
        $data = file_get_contents("php://input");
        //var_dump($data);exit;
        $xml=simplexml_load_string($data);
        //var_dump($xml);exit;
        $event=$xml->Event;
        //var_dump($event);exit;
        if($event=='subscribe'){
            $openid=$xml->FromUserName;
            $sub_time=$xml->CreateTime;
            //获取用户信息
            $user_info=$this->getUserInfo($openid);
            $userRes=WxModel::where(['openid'=>$openid])->first();
            if($userRes){
                echo "用户已存在";
            }else{
                echo $user_info['nickname'];exit;
                $user_data=[
                  'openid'=>$openid,
                  'add_time'=>time(),
                    'nickname'=>$user_info['nickname'],
                    'sex'=>$user_info['sex'],
                    'headimgurl'=>$user_info['headimgurl'],
                    'subscribe_time'=>$sub_time,
                ];
                $id=WxModel::insertGetId($user_data);
            }
        }
        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log',$log_str,FILE_APPEND);
    }
    //获取AccessToken
    public function getAccessToken(){
        $token=Redis::get($this->redis_weixin_access_token);
        if(!$token){
            $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".env('WEIXIN_APPID')."&secret=".env('WEIXIN_APPSECRET');
            $data=json_decode(file_get_contents($url),true);
            //记录缓存
            $token=$data['access_token'];
            Redis::set($this->redis_weixin_access_token,$token);
            Redis::setTimeout($this->redis_weixin_access_token,3600);
        }
        return $token;
    }
    // 获取用户信息
    public function getUserInfo($openid)
    {
        $access_token = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $data = json_decode(file_get_contents($url),true);
        return $data;
    }
}