<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/4/17
 * Time: 15:59
 */

namespace app\app\controller;
use app\app\controller\AppBase;

class Web extends AppBase
{
    /**
     * 获取首页的轮播图
     */
    public  function index(){
        $data = input();
        $return['code'] = 100;
        $return['msg'] = '';
        $list  = model('slideshow')->getAppList($data);
        if(is_array($list) && !empty($list)){
            unset($return['msg']);
            $return['code'] = 200;
            $return['data'] = $list;
        }elseif(is_array($list) && empty($list)){
            unset($return['msg']);
            $return['code'] = 200;
            $return['data'] = null;
        }else{
            $return['msg'] = '数据获取失败!';
        }
        return json($return);
    }

    /**
     * 关于我们
     * @return \think\response\Json
     */
    public function  about()
    {
        $data  = input();
        $token = check_token($data);//验证Token
        if ($token) {
            $return['code'] = 100;
            $return['msg']  = '';
            $list           = model('about')->getAbout($data);
            if ($list) {
                unset($return['msg']);
                $return['code'] = 200;
                $return['data'] = $list;
            }else {
                $return['msg'] ='数据获取失败!';
            }
        }else {
            $return['code'] = 300;
            $return['msg'] = 'token已失效!';
        }
        return json($return);
    }



    /**
     * 协议
     * @return \think\response\View
     */
    public  function agreement(){
        $data = input();
        $return['code'] = 100;
        $return['msg'] = '获取数据失败!';
        $return['data'] = null;
        $bool               = model('web')->where(['id'=>1])->field('agreement')->find();
        if($bool){//删除原图
            $return['code'] = 200;
            $return['msg'] = '获取数据成功!';
            $return['data'] = $bool;
        }
        return  json($return);

    }












}