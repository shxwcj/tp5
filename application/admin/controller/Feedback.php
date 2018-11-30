<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/4/12
 * Time: 11:45
 */

namespace app\admin\controller;
use app\admin\controller\AdminBase;

class Feedback  extends  AdminBase
{
    /**
     * 意见反馈列表
     */
    public function index(){
        $data  = input();
        $list = model('feedback')->getList($data);
        return view(['lists'=>$list]);
    }

    /**
     * 意见详情
     */
    public  function edit(){
        $data  = input();
        $list = model('feedback')->info($data);
        return view(['lists'=>$list]);
    }

    /**
     * 单个删除反馈
     */
    public function del(){
        $data  = input();
        $re = model('feedback')->del($data);
        if($re >0){
            Api()->setApi('url',url('Feedback/index',['page'=>input('page')]))->ApiSuccess($re);
        }else{
            Api()->setApi('msg',$re)->setApi('url',0)->ApiError();
        }
    }
    /**
     * 批量删除反馈
     */
    public function delAll(){
        $data  = input();
        $re = model('feedback')->delALL($data);
        if($re >0){
            Api()->setApi('url',url('Feedback/index',['page'=>input('page')]))->ApiSuccess($re);
        }else{
            Api()->setApi('msg',$re)->setApi('url',0)->ApiError();
        }
    }
}