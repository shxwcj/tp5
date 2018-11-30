<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/4/12
 * Time: 16:15
 */

namespace app\admin\controller;
use app\admin\controller\AdminBase;

class Help extends AdminBase
{
    /**
     * 帮助列表
     */
    public function index(){
        $data  = input();
        $list = model('help')->getListPc($data);
        return view([
            'lists'=>$list,
        ]);
    }

    /**
     * 帮助添加
     */
    public function add(){
        $data  = input();
        if(request()->isAjax()){
            $list = model('help')->add($data);
            if($list >0){
                Api()->setApi('url',url('Help/index'))->ApiSuccess($list);
            }else{
                Api()->setApi('msg',$list)->setApi('url',0)->ApiError();
            }
        }
        return view();
    }

    /**
     * 帮助编辑
     */
    public function edit(){
        $data  = input();
        $list = model('help')->info($data);
        if(request()->isAjax()){
            $list = model('help')->edit($data);
            if($list >0){
                Api()->setApi('url',url('Help/index',['page'=>input('page')]))->ApiSuccess($list);
            }else{
                Api()->setApi('msg',$list)->setApi('url',0)->ApiError();
            }
        }

        return view([
            'lists'=>$list,
        ]);
    }
    /**
     * 单个删除帮助
     */
    public function del(){
        $data  = input();
        $re = model('help')->del($data);
        if($re >0){
            Api()->setApi('url',url('help/index',['page'=>input('page')]))->ApiSuccess($re);
        }else{
            Api()->setApi('msg',$re)->setApi('url',0)->ApiError();
        }
    }
    /**
     * 批量删除帮助
     */
    public function delAll(){
        $data  = input();
        $re = model('help')->delALL($data);
        if($re >0){
            Api()->setApi('url',url('help/index',['page'=>input('page')]))->ApiSuccess($re);
        }else{
            Api()->setApi('msg',$re)->setApi('url',0)->ApiError();
        }
    }
}