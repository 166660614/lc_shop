<?php

namespace App\Admin\Controllers;

use App\Model\WxModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use GuzzleHttp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class WxController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    protected $redis_weixin_access_token='astr:weixin_access_token';//微信 access_token
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('gdescription')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        $user_id=$_GET['user_id'];
       /* return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());*/
        return $content
            ->header('Create')
            ->description('description')
            ->body(view('admin.chat',['user_id' =>$user_id])->render());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WxModel);

        $grid->id('Id')->display(function ($id){
            return '100'.$id;
        });
        $grid->uid('Uid');
        $grid->nickname('Nickname');
        $grid->openid('Openid');
        $grid->add_time('Add time')->display(function ($time){
            return date('Y-m-d H:i:s',$time);
        });
        $grid->sex('Sex')->display(function ($sex){
            if($sex==0){
                $asex='未知';
            }elseif($sex==1){
                $asex='男';
            }else{
                $asex='女';
            }
            return $asex;
        });
        $grid->headimgurl('Headimgurl')->display(function ($img){
            return "<img src='$img'>";
        });
        $grid->subscribe_time('Subscribe time')->display(function ($time){
            return date('Y-m-d H:i:s',$time);
        });
        $grid->actions(function ($actions){
            $key=$actions->getKey();
            $actions->prepend('<a href="/admin/weixin/userinfo/create?user_id='.$key.'"><i class="fa fa-paper-plane"></i></a>');
        });
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(WxModel::findOrFail($id));

        $show->id('Id');
        $show->uid('Uid');
        $show->nickname('Nickname');
        $show->openid('Openid');
        $show->add_time('Add time');
        $show->sex('Sex');
        $show->headimgurl('Headimgurl');
        $show->subscribe_time('Subscribe time');
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {

    }
    protected function privatechat(Request $request){
        $url='https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$this->getAccessToken();
        $news=$request->input('news');
        $user_id=$request->input('user_id');
        $where=[
          'id'=>$user_id,
        ];
        $openid=WxModel::where($where)->value('openid');
        //请求微信接口
        $client = new GuzzleHttp\Client(['base_uri' => $url]);
        $data=[
            'touser'=>$openid,
            'msgtype'=>'text',
            'text'=>[
                'content'=>$news
            ]
        ];
        $r=$client->request('post',$url,['body'=>json_encode($data,JSON_UNESCAPED_UNICODE)]);
        //解析接口返回信息
        $response_arr=json_decode($r->getBody(),true);
        if($response_arr['errcode']==0){
            $arr=[
              'code'=>0,
              'msg'=>'发送成功',
            ];
            echo json_encode($arr);
        }else{
            echo $response_arr['msg'];
        }
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
}
