<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/4/21
 * Time: 17:15
 */

namespace app\app\controller;


class Massage extends  AppBase
{
    /**
     * 查询消息
     * @param $data
     */
    public function getMessage(){
        $data = input();
        $token  = check_token($data);//验证Token
        $return['code'] = 100;
        $return['msg']  = '';
        if ($token) {
            $bool = model('push')->getMessage($data);
            if(is_array($bool) && !empty($bool)){
                unset($return['msg']);
                $return['code'] = 200;
                $return['data'] = $bool;
            }elseif(is_array($bool) && empty($bool)){
                unset($return['msg']);
                $return['code'] = 200;
                $return['data'] = null;
            }else{
                $return['msg']  = $bool;
            }
        }else {
            $return['code'] = 300;
            $return['msg']  = 'token已失效!';
        }
        return  json($return);
    }

    /**
     * 批量删除消息
     */
    public function  delMessage(){
        $data = input();
        $token  = check_token($data);//验证Token
        $return['code'] = 100;
        $return['msg']  = '';
        if ($token) {
            $bool = model('push')->delMessage($data);
            if($bool > 0){
                unset($return['msg']);
                $return['code'] = 200;
                $return['data'] = '删除成功!';
            }elseif($bool){
                $return['msg']  = $bool;
            }else{
                $return['msg']  = '删除失败!';
            }
        }else {
            $return['code'] = 300;
            $return['msg']  = 'token已失效!';
        }
        return  json($return);
    }



}