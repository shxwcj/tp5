<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/4/16
 * Time: 10:55
 */

namespace app\admin\controller;
use app\admin\controller\AdminBase;

class Promote  extends  AdminBase
{
    /**
     * 首页轮播
     * @return \think\response\View
     */
    public  function index(){
        $data = input();
        $list  = model('Slideshow')->getList($data);
        return view(['lists'=>$list]);
    }

    /**
     * 添加轮播图
     */
    public function  addSlideShow(){
        $data = input();
      if(request()->isAjax()){
          if(empty($data['img_name'])) return '填写图片名称!';
          if(empty($data['path'])) return '上传图片!';
          $data['path'] =  uploadBase64($data['path'],'slideShow');//上传图片
          $img = check_image($data['path']);//检测是否上传成功!
          if($img){
              $bool  = model('Slideshow')->addSlideShow($data);
              if($bool>0){
                  Api()->setApi('url',url('Promote/index'))->ApiSuccess($bool);
              }else{
                  Api()->setApi('msg',$bool)->setApi('url',0)->ApiError();
              }
          }else{
              Api()->setApi('msg',$img)->setApi('url',0)->ApiError();
          }
      }
        return view();
    }

    /**
     * app轮播图启用/禁用
     */
    public function change_status(){
        $data  = input();
        $bool   = model('Slideshow')->change_status($data);
        if($bool>0){
            Api()->setApi('url',url('Promote/index',['page'=>input('page')]))->ApiSuccess($bool);
        }else{
            Api()->setApi('msg',$bool)->setApi('url',0)->ApiError();
        }
    }

    /**
     *单个轮播图删除
     */
    public function del(){
        $data  = input();
        $bool   = model('Slideshow')->del($data);
        if($bool>0){
            Api()->setApi('url',url('Promote/index',['page'=>input('page')]))->ApiSuccess($bool);
        }else{
            Api()->setApi('msg',$bool)->setApi('url',0)->ApiError();
        }
    }
    /**
     *批量轮播图删除
     */
    public function delAll(){
        $data  = input();
        $bool   = model('Slideshow')->delAll($data);
        if($bool>0){
            Api()->setApi('url',url('Promote/index'))->ApiSuccess($bool);
        }else{
            Api()->setApi('msg',$bool)->setApi('url',0)->ApiError();
        }
    }
    /**
     * 关于我们
     * @return \think\response\View
     */
    public  function about(){
        $data  =  input();
        if(request()->isAjax()){
            $status['status'] = 0;
            $status['img'] = '';
            if(empty($data['describe'])) return '填写内容!';
            if(!empty($data['logo'])){
                $data['path'] =  uploadBase64($data['logo'],'slideShow');//上传图片
                $img = check_image($data['path']);//检测是否上传成功!
                if($img){
                    $old[0] =$data['oldImage'];
                    deleteImgAll($old);
                }
            }else{
                unset($data['path']);
                $img  =1;
            }
            if($img){
                $bool  = model('about')->add($data);
                if($bool>0){
                    $status['status'] = 1;
                    if(isset($data['path'])){
                        $status['img'] = $data['path'];
                    }else{
                        $status['img'] = '';
                    }
                }
            }
            return json($status);
        }
        $info  = model('about')->where('')->find();
        return view(['info'=>$info]);
    }
}