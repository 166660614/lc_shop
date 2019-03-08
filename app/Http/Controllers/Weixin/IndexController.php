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
        $user_info = json_encode($this->getUserInfo($openid));//获取用户信息
        if (isset($MsgType)) {
            if ($MsgType == 'event') {
                if ($event == 'subscribe') {
                    //Redis 缓存用户数据
                    Redis::set('userinfo',$user_info);
                    $xml_response = '<xml><ToUserName><![CDATA['.$openid .']]></ToUserName><FromUserName><![CDATA[' . $xml->ToUserName .']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[欢迎关注本公众号]]></Content></xml>';
                    echo $xml_response;
                }
            }
        }
        $log_str = date('Y-m-d H:i:s') . "\n" . $postdata .$user_info."\n<<<<<<<";
        file_put_contents('logs/wx_event.log', $log_str, FILE_APPEND);
    }
    public function viewRedisUsers(){
        $userinfo=Redis::get('userinfo');
            $userinfo=json_decode($userinfo,true);
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
    //黑名单
    public function getBlackList(Request $request){
        $url="https://api.weixin.qq.com/cgi-bin/tags/members/batchblacklist?access_token=".$this->getAccessToken();
        $array=$request->input('openid');
        $data=[
            'openid_list'=>$array
        ];
        $client = new GuzzleHttp\Client(['base_uri' => $url]);
        $r=$client->request('post',$url,['body'=>json_encode($data,JSON_UNESCAPED_UNICODE)]);
        //解析接口返回信息
        $response_arr=json_decode($r->getBody(),true);
        if($response_arr['errcode']){
            $res=[
                'msg'=>'加入黑名单成功',
                'code'=>0
            ];
        }else{
            $res=[
              'msg'=>$response_arr['errmsg'],
              'code'=>1
            ];
        }
        echo json_encode($res);
    }
    //添加用户标签
    public function addTag(Request $request){
        $url="https://api.weixin.qq.com/cgi-bin/tags/create?access_token=".$this->getAccessToken();
        $tag_name=$request->input('tag_name');
        $data=[
            'tag'=>[
                'name'=>$tag_name,
            ]
        ];
        $client = new GuzzleHttp\Client(['base_uri' => $url]);
        $r=$client->request('post',$url,['body'=>json_encode($data,JSON_UNESCAPED_UNICODE)]);
        //解析接口返回信息
        $response_arr=json_decode($r->getBody(),true);
        var_dump($response_arr);exit;
        if($response_arr['id']){
            $res=[
                'msg'=>'加入黑名单成功',
                'code'=>0
            ];
        }else{
            $res=[
                'msg'=>$response_arr['errmsg'],
                'code'=>1
            ];
        }
        echo json_encode($res);
    }
//    public function getTag(){
//        $url="https://api.weixin.qq.com/cgi-bin/tags/get?access_token=".$this->getAccessToken();
//
//    }
}