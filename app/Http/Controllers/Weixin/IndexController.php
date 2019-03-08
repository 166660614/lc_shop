<?php

namespace App\Http\Controllers\Weixin;

use App\Model\WxModel;
use App\Model\ChatRecordModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp;
use App\Model\WxMediaModel;
use Illuminate\Support\Facades\Storage;
class IndexController extends Controller
{
    protected $redis_weixin_access_token='astr:weixin_access_token';//微信 access_token


    public function validToken1(){
        echo $_GET['echostr'];
    }
    //接受微信服务器事件推送
    public function wxEvent()
    {
        $postdata = file_get_contents("php://input");
        $xml = simplexml_load_string($postdata);
        $MsgType = $xml->MsgType;
        $event = $xml->Event;
        $openid = $xml->FromUserName; //用户openid
        //$sub_time = $xml->CreateTime; //关注时间
        $user_info = $this->getUserInfo($openid);//获取用户信息
        if (isset($MsgType)) {
            if ($MsgType == 'event') {
                if ($event == 'subscribe') {
                    //Redis 缓存用户数据
                    Redis::set('userinfo',$user_info);
                }
            }
        }
        $log_str = date('Y-m-d H:i:s') . "\n" . $postdata . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log', $log_str, FILE_APPEND);
    }
    public function viewRedisUsers(){
        $userinfo=Redis::get('userinfo');
        $data=[
          'userinfo'=>$userinfo
        ];
        return view('userinfo.redis',$data);
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