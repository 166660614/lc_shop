<?php

namespace App\Admin\Controllers;

use App\Model\WxModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class WxController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
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
        });;

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
        $form = new Form(new WxModel);

        $form->number('uid', 'Uid');
        $form->text('nickname', 'Nickname');
        $form->text('openid', 'Openid');
        $form->number('add_time', 'Add time');
        $form->switch('sex', 'Sex');
        $form->text('headimgurl', 'Headimgurl');
        $form->number('subscribe_time', 'Subscribe time');

        return $form;
    }
}
