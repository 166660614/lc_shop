<?php

namespace App\Admin\Controllers;

use App\Model\PmMediaModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use GuzzleHttp;
use Illuminate\Support\Facades\Redis;

class PmMediaController extends Controller
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
            ->description('description')
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
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PmMediaModel);

        $grid->id('Id');
        $grid->openid('Openid');
        $grid->add_time('Add time');
        $grid->msg_type('Msg type');
        $grid->media_id('Media id');
        $grid->format('Format');
        $grid->msg_id('Msg id');
        $grid->local_file_name('Local file name');
        $grid->local_file_path('Local file path');

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
        $show = new Show(PmMediaModel::findOrFail($id));

        $show->id('Id');
        $show->openid('Openid');
        $show->add_time('Add time');
        $show->msg_type('Msg type');
        $show->media_id('Media id');
        $show->format('Format');
        $show->msg_id('Msg id');
        $show->local_file_name('Local file name');
        $show->local_file_path('Local file path');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new PmMediaModel);

        $form->file('file_column');
        return $form;
    }
    protected  function addnews(Request $request){
        $file_column=$request->file('file_column');
        $file_name=$file_column->getClientOriginalName();//获得文件原名称
        $file_ext=$file_column->getClientOriginalExtension();//获得文件后缀类型
        //重命名
        $file_new_name=str_random(15).'.'.$file_ext;

        //保存文件
        $save_file_path = $request->file('file_column')->storeAs('file_image',$file_new_name);//保存本地服务器后的路径
        $this->upWxMedia($save_file_path,$file_column);
    }

    //上传文件至微信服务器
    protected function upWxMedia($path,$file_column){
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token='.$this->getAccessToken().'&type=image';
        $client = new GuzzleHttp\Client();
        $response = $client->request('POST',$url,[
            'multipart' => [
                [
                    'name'     => 'media',
                    'contents' => fopen($path, 'r')
                ],
            ]
        ]);
        $body = $response->getBody();
        $arr= json_decode($body,true);//图片media_id 和查看图片路径(永久)
        $this->getWxPMedia($arr['media_id'],$client);//获取微信服务器的永久素材保存至数据库
    }

    //获取微信服务器的永久素材保存至数据库
    protected function getWxPMedia($media_id,$client){
        $url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token='.$this->getAccessToken();
        $response = $client->request('POST',$url,[
            $data=[
                'media'=>$media_id,
            ]
        ]);
        $body = $response->getBody();
        $arr= json_decode($body,true);
        print_r($arr);exit;
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
    //获取永久素材列表
    protected function getPMediaList(){
        $client = new GuzzleHttp\Client();
        $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token='.$this->getAccessToken();
        $body = [
            "type"      => 'image',
            "offset"    => 0,
            "count"     => 20
        ];
        $response = $client->request('POST', $url, [
            'body' => json_encode($body)
        ]);
        $body = $response->getBody();
        echo $body;exit;
        $arr = json_decode($response->getBody(),true);
        var_dump($arr);exit;
    }
}
