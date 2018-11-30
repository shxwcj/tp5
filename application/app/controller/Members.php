<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/4/9
 * Time: 18:30
 */

namespace app\app\controller;

use app\app\controller\AppBase;
use app\common\model\Feedback;
class Members extends  AppBase
{
    /**
     * 忘记密码
     */
    public function forgetPassword(){
        $data     = input();
        $return['code'] = 100;
        $return['msg'] = '';
        $account  = check_mobile($data['account']);//验证手机号码
        if($account){
            $code  =  check_code($data);//验证验证码
            if($code){
                $bool  = model('members')->edit_password($data);
                if(isset($bool['member_id'])){
                    session('member',$bool);
                    unset( $return['msg']);
                    $return['code'] = 200;
                    $return['data'] = $bool;
                }else{
                    $return['msg']  = $bool;
                }
            }else{
                $return['msg'] = '验证码不正确';
            }
        }else{
            $return['code'] = 300;
            $return['msg'] = 'token已失效!';
        }
        return json($return);
    }

    /**
     * 修改头像
     */
    public function edit_image(){
        $data            = input();
        $return['code'] = 100;
        $return['msg'] = '';
        $token  = check_token($data);//验证Token
        if($token) {
            $info = app_upload_base64($data['image'],'headimage',$data['type']);// uploadBase64($data['image'], 'headimage');
            $bool = check_image($info);
            if ($bool) {
                $member =  model('members')->where(['member_id' => $data['member_id']])->field('member_id,image')->find();//获取原图片
                $xiang  = model('members')->save(['image' => $info], ['member_id' => $member['member_id']]);
                if ($xiang > 0) {
                    if ($member['image']) {//删除原图片
                        delete_one_img($member['image']);
                    }
                    unset($return['msg']);
                    $return['code'] = 200;
                    $return['data'] = $info;
                } else {
                    $return['msg'] = '头像修改失败!';
                }
            }
        }else{
            $return['code'] = 300;
            $return['msg'] = 'token已失效!';
        }
        return json($return);
    }

    /**
     * 修改密码
     */
    public function editPassword(){
        $data     = input();
        $return['code'] = 100;
        $return['msg'] = '';
        $account  = check_mobile($data['account']);//验证手机号码
        if($account){
            if($data['new_password1'] == $data['new_password2']){
                $token  = check_token($data);//验证Token
                if($token) {
                    $bool = model('members')->editPassword($data);
                    if ($bool > 0) {
                        $return['code'] = 200;
                        $return['msg']  = '修改成功';
                    }
                    else {
                        $return['msg'] = $bool;
                    }
                }else{
                    $return['code'] = 300;
                    $return['msg'] = 'token已失效!';
                }
            }else{
                $return['msg'] = '两次密码不一致!';
            }
        }else{
            $return['msg'] = '手机号码不正确';
        }
        return json($return);
    }

    /**
     * 修改昵称
     */
    public  function editName(){
        $data     = input();
        $return['code'] = 100;
        $return['msg'] = '';
        if(isset($data['membername'])){
            $data['membername'] = trim($data['membername']);
            if(!empty($data['membername'])){
                $token  = check_token($data);//验证Token
                if($token) {
                    $bool = model('members')->editName($data);
                    if ($bool > 0) {
                        unset($return['msg']);
                        $return['code'] = 200;
                        $return['data']  = $data['membername'];
                    }else {
                        $return['msg'] = $bool;
                    }
                }else{
                    $return['code'] = 300;
                    $return['msg'] = 'token已失效!';
                }
            }else{
                $return['msg'] = '昵称不能为空!';
            }
        }else{
            $return['msg'] = '昵称不能为空!';
        }
        return json($return);
    }

    /**
     * 修改性别
     */
    public  function editSex(){
        $data            = input();
        $return['code'] = 100;
        $return['msg']  = '';
        $token  = check_token($data);//验证Token
        if($token) {
            $bool = model('members')->editSex($data);
            if ($bool > 0) {
                unset($return['msg']);
                $return['code'] = 200;
                $return['data']  = '修改成功!';
            }else {
                $return['msg'] = '修改失败!';
            }
        }else{
            $return['code'] = 300;
            $return['msg'] = 'token已失效!';
        }
        return json($return);
    }

    /**
     * 修改年龄
     */
    public  function editAge(){
        $data            = input();
        $return['code'] = 100;
        $return['msg']  = '';
        $token  = check_token($data);//验证Token
        if($token) {
            $bool = model('members')->editAge($data);
            if ($bool > 0) {
                unset($return['msg']);
                $return['code'] = 200;
                $return['data']  = '修改成功!';
            }else {
                $return['msg'] = '修改失败!';
            }
        }else{
            $return['code'] = 300;
            $return['msg'] = 'token已失效!';
        }
        return json($return);
    }

    /**
     * 意见反馈
     */
    public function feedback(){
        $data            = input();
        $return['code'] = 100;
        $return['msg']  = '';
        $token  = check_token($data);//验证Token
        if($token) {
            $bool = model('feedback')->addFeedback($data);
            if ($bool > 0) {
                unset($return['msg']);
                $return['code'] = 200;
                $return['data']  = '反馈成功!';
            }else {
                $return['msg'] = $bool;
            }
        }else{
            $return['code'] = 300;
            $return['msg'] = 'token已失效!';
        }
        return json($return);
    }

    /**
     * 帮助指引
     */
    public function help(){
        $data            = input();
        $return['code'] = 100;
        $return['msg']  = '';
        $token  = check_token($data);//验证Token
        if($token) {
            $bool = model('help')->getList($data);
            if (is_array($bool)) {
                unset($return['msg']);
                $return['code'] = 200;
                if(!empty($bool)){
                    $return['data']  = $bool;
                }else{
                    $return['data']  = null;
                }
            }else {
                $return['msg'] = '获取失败!';
            }
        }else{
            $return['code'] = 300;
            $return['msg'] = 'token已失效!';
        }
        return json($return);
    }

    /**
     * 关于我们
     */
    public function about(){
        $data            = input();
        $return['code'] = 100;
        $return['msg']  = '';
        $token  = check_token($data);//验证Token
        if($token) {
            unset($return['msg']);
            $bool = model('web')->getAbout($data);//  $bool = model('about')->where('')->find();
            $return['code'] = 200;
            $return['data']  = $bool;
        }else{
            $return['code'] = 300;
            $return['msg'] = 'token已失效!';
        }
        return json($return);
    }

    /**
     * 用户详情
     */
    public  function memberInfo(){
        $data            = input();
        $return['code'] = 100;
        $return['msg']  = '';
        $token  = check_token($data);//验证Token
        if($token) {
            unset($return['msg']);
            $bool = model('members')->memberInfo($data);
            $return['code'] = 200;
            $return['data']  = $bool;
        }else{
            $return['code'] = 300;
            $return['msg'] = 'token已失效!';
        }
        return json($return);
    }
}