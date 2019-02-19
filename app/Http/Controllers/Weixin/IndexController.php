<?php

namespace App\Http\Controllers\Weixin;

use App\Model\WxModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp;
class IndexController extends Controller
{
    protected $redis_weixin_access_token='astr:weixin_access_token';//微信 access_token
    //接受微信服务器事件推送
    public function wxEvent(){
        $data = file_get_contents("php://input");
        $xml=simplexml_load_string($data);
        $event=$xml->Event;
        $openid=$xml->FromUserName; //用户openid
        $sub_time=$xml->CreateTime; //关注时间
        $user_info=$this->getUserInfo($openid);//获取用户信息
        if($event=='subscribe'){
            $userRes=WxModel::where(['openid'=>$openid])->first();
            if($userRes){
                //用户已存在;
                $user_where=['openid'=>$openid];
                $user_update=[
                    'nickname'=>$user_info['nickname'],
                    'sex'=>$user_info['sex'],
                    'headimgurl'=>$user_info['headimgurl'],
                    'subscribe_time'=>$sub_time,
                ];
                $res=WxModel::where($user_where)->update($user_update);
            }else{
                //用户不存在
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
    //自定义菜单
    public function zdyMenus(){
        $access_token = $this->getAccessToken();
        $url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;
        //请求微信接口
        $client = new Client([
            'base_uri' => $url,
        ]);

        $data=[
          'button'=>[
            [
                'type'=>'view',
                'name'=>'鑫恒科技',
                'url'=>'http://www.52self.cn'
            ]
          ]
        ];
        $r=$client->request('post',$url,['body'=>json_encode($data)]);

        //解析接口返回信息
        $response_arr=json_decode($r->getBody(),true);
        if($response_arr['errcode'==0]){
            echo "菜单创建成功";
        }else{
            echo "菜单创建失败，请重试";
            echo "<br/>";
            echo $response_arr['errmsg'];
        }
    }
}