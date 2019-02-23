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
        $sub_time = $xml->CreateTime; //关注时间
        $user_info = $this->getUserInfo($openid);//获取用户信息
        if (isset($MsgType)) {
            if ($MsgType == 'text') {
                $msg = $xml->Content;
                //存入数据库
                $data=[
                    'content'=>$msg,
                    'add_time'=>time(),
                    'openid'=>$openid,
                    'nickname'=>$user_info['nickname'],
                ];
                $res=ChatRecordModel::insert($data);
            } elseif ($MsgType == 'image') {
                $file_name=$this->dealWxImg($xml->MediaId);
                $data=[
                    'openid'=>$openid,
                    'add_time'=>time(),
                    'msg_type'=>$MsgType,
                    'media_id'=>$xml->MediaId,
                    'format'=>$xml->Format,
                    'msg_id'=>$xml->MsgId,
                    'local_file_name'=>$file_name
                ];
                $m_id = WxMediaModel::insertGetId($data);
                var_dump($m_id);
                $xml_response = '<xml><ToUserName><![CDATA[' . $openid . ']]></ToUserName><FromUserName><![CDATA[' . $xml->ToUserName . ']]></FromUserName><CreateTime>' . time() . '</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[' . '时间是 ：' . date('Y-m-d H:i:s') . ']]></Content></xml>';
                echo $xml_response;
            } elseif ($MsgType == 'voice') {
                $file_name=$this->dealWxVoice($xml->MediaId);
                $data=[
                    'openid'=>$openid,
                    'add_time'=>time(),
                    'msg_type'=>$MsgType,
                    'media_id'=>$xml->MediaId,
                    'format'=>$xml->Format,
                    'msg_id'=>$xml->MsgId,
                    'local_file_name'=>$file_name
                ];
                $m_id = WxMediaModel::insertGetId($data);
                var_dump($m_id);
                $xml_response = '<xml><ToUserName><![CDATA[' . $openid . ']]></ToUserName><FromUserName><![CDATA[' . $xml->ToUserName . ']]></FromUserName><CreateTime>' . time() . '</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[' . '时间是 ：' . date('Y-m-d H:i:s') . ']]></Content></xml>';
                echo $xml_response;
            } elseif ($MsgType == 'event') {
                if ($event == 'subscribe') {
                    $userRes = WxModel::where(['openid' => $openid])->first();
                    if ($userRes) {
                        //用户已存在;
                        $user_where = ['openid' => $openid];
                        $user_update = [
                            'nickname' => $user_info['nickname'],
                            'sex' => $user_info['sex'],
                            'headimgurl' => $user_info['headimgurl'],
                            'subscribe_time' => $sub_time,
                        ];
                        $res = WxMediaModel::where($user_where)->update($user_update);
                    } else {
                        //用户不存在
                        $user_data = [
                            'openid' => $openid,
                            'add_time' => time(),
                            'nickname' => $user_info['nickname'],
                            'sex' => $user_info['sex'],
                            'headimgurl' => $user_info['headimgurl'],
                            'subscribe_time' => $sub_time,
                        ];
                        $id = WxModel::insertGetId($user_data);
                    }
                } elseif ($event == 'CLICK') {
                    if ($xml->EventKey == 'kefu01') {
                        $this->kefu01($openid, $xml->ToUserName);
                    }
                }
            } elseif ($MsgType=='video') {
                $file_name=$this->dealWxVideo($xml->MediaId);
                $data=[
                    'openid'=>$openid,
                    'add_time'=>time(),
                    'msg_type'=>$MsgType,
                    'media_id'=>$xml->MediaId,
                    'format'=>$xml->Format,
                    'msg_id'=>$xml->MsgId,
                    'local_file_name'=>$file_name
                ];
                $m_id = WxMediaModel::insertGetId($data);
                var_dump($m_id);
                $xml_response = '<xml><ToUserName><![CDATA[' . $openid . ']]></ToUserName><FromUserName><![CDATA[' . $xml->ToUserName . ']]></FromUserName><CreateTime>' . time() . '</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[' . '时间是 ：' . date('Y-m-d H:i:s') . ']]></Content></xml>';
                echo $xml_response;
            }
        }
        $log_str = date('Y-m-d H:i:s') . "\n" . $postdata . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log', $log_str, FILE_APPEND);
    }
    //客服处理
    public function kefu01($openid,$from){
        $xml_response='<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$from.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['.'Hello 现在时间是'.date('Y-m-d H:i:s').']]></Content></xml>';
        echo $xml_response;
    }
    //图片信息处理
    public function dealWxImg($media_id){
        $file_name=$this->imgVoiceVideo($param='image',$media_id);
        return $file_name;
    }
    //处理语音消息
    public function dealWxVoice($media_id){
        $file_name=$this->imgVoiceVideo($param='voice',$media_id);
        return $file_name;
    }
    //处理视频消息
    public function dealWxVideo($media_id){
        $file_name=$this->imgVoiceVideo($param='video',$media_id);
        return $file_name;
    }
    //保存语音视频图片
    public function imgVoiceVideo($param,$media_id){
        $url='https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getAccessToken().'&media_id='.$media_id;
        //发送Http请求
        $client=new GuzzleHttp\Client();
        $response=$client->get($url);
        //var_dump($response);exit;
        //获取文件名
        $file_info=$response->getHeader('Content-disposition');
        //var_dump($file_info);exit;
        $file_name=substr(rtrim($file_info[0],'"'),-20);
        //var_dump($file_name);exit;
        $wx_image_path='wx/'.$param.'/'.$file_name;
        //var_dump($wx_image_path);exit;
        //保存语音文件
        $res=Storage::disk('local')->put($wx_image_path,$response->getBody());
        if($res){

        }else{

        }
        return $file_name;
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
        $client = new GuzzleHttp\Client(['base_uri' => $url]);

        $data=[
            'button'=>[
                [
                    'name'=>'鑫恒科技',
                    'sub_button'=>[
                        [
                            'type'=>'view',
                            'name'=>'我要登录',
                            'url'=>'http://lcshop.52self.cn/login'
                        ],
                        [
                            'type'=>'view',
                            'name'=>'注册账号',
                            'url'=>'http://lcshop.52self.cn/register'
                        ],
                        [
                            'type'=>'view',
                            'name'=>'网站首页',
                            'url'=>'http://lcshop.52self.cn'
                        ]
                    ]
                ],
                [
                    'name'=>'鑫恒科技',
                    'sub_button'=>[
                        [
                            'type'=>'view',
                            'name'=>'二级菜单',
                            'url'=>'http://lcshop.52self.cn/login'
                        ],
                        [
                            'type'=>'view',
                            'name'=>'二级菜单',
                            'url'=>'http://lcshop.52self.cn/register'
                        ],
                        [
                            'type'=>'view',
                            'name'=>'一级菜单',
                            'url'=>'http://lcshop.52self.cn'
                        ]
                    ]
                ],
                [
                    'name'=>'客服服务',
                    'type'=>'click',
                    'key'=>'kefu01',
                ]
            ]
        ];
        $r=$client->request('post',$url,['body'=>json_encode($data,JSON_UNESCAPED_UNICODE)]);

        //解析接口返回信息
        $response_arr=json_decode($r->getBody(),true);
        var_dump($response_arr);
        if($response_arr['errcode']==0){
            echo "菜单创建成功";
        }else{
            echo "菜单创建失败，请重试";
            echo "<br/>";
        }
    }
    //删除自定义菜单
    public function delMenus(){
        $access_token = $this->getAccessToken();
        $url='https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$access_token;
        $client = new GuzzleHttp\Client(['base_uri' => $url]);//请求微信接口

        $data=[
            'button'=>[
                [
                    'name'=>'鑫恒科技',
                    'sub_button'=>[
                        [
                            'type'=>'view',
                            'name'=>'我要登录',
                            'url'=>'http://lcshop.52self.cn/login'
                        ],
                        [
                            'type'=>'view',
                            'name'=>'注册账号',
                            'url'=>'http://lcshop.52self.cn/register'
                        ],
                        [
                            'type'=>'view',
                            'name'=>'网站首页',
                            'url'=>'http://lcshop.52self.cn'
                        ]
                    ]
                ],
                [
                    'name'=>'鑫恒科技',
                    'sub_button'=>[
                        [
                            'type'=>'view',
                            'name'=>'二级菜单',
                            'url'=>'http://lcshop.52self.cn/login'
                        ],
                        [
                            'type'=>'view',
                            'name'=>'二级菜单',
                            'url'=>'http://lcshop.52self.cn/register'
                        ],
                        [
                            'type'=>'view',
                            'name'=>'一级菜单',
                            'url'=>'http://lcshop.52self.cn'
                        ]
                    ]
                ],
                [
                    'name'=>'客服服务',
                    'type'=>'click',
                    'key'=>'kefu01',
                ]
            ]
        ];
        $r=$client->request('post',$url,['body'=>json_encode($data,JSON_UNESCAPED_UNICODE)]);
        //解析接口返回信息
        $response_arr=json_decode($r->getBody(),true);
        var_dump($response_arr);
        if($response_arr['errcode']==0){
            echo "菜单删除成功";
        }else{
            echo "菜单删除失败，请重试";
            echo "<br/>";
            echo $response_arr['errmsg'];
        }
    }
    //群发文本消息
    public function sendTextAll(){
        $url='https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token='.$this->getAccessToken();

        //请求微信接口
        $client = new GuzzleHttp\Client(['base_uri' => $url]);
        $data=[
            'filter'=>[
                'is_to_all'=>true,
            ],
            'text'=>[
                'content'=>'打扰了！2ddd'
            ],
            'msgtype'=>'text'
        ];
        $r=$client->request('post',$url,['body'=>json_encode($data,JSON_UNESCAPED_UNICODE)]);
        //解析接口返回信息
        $response_arr=json_decode($r->getBody(),true);
        var_dump($response_arr);
        if($response_arr['errcode']==0){
            echo "群发成功";
        }else{
            echo "群发失败，请重试";
            echo "<br/>";
        }

    }
    //api次数清零
    public function clearApi(){
        $url='https://api.weixin.qq.com/cgi-bin/clear_quota?access_token='.$this->getAccessToken();
        //请求微信接口
        $client = new GuzzleHttp\Client();
        $response = $client->request('POST',$url,[
            $data=[
                'appid'=>env('WEIXIN_APPID'),
            ]
        ]);
        $body = $response->getBody();
        $arr= json_decode($body,true);
        print_r($arr);exit;
    }
}