<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/4/9
 * Time: 10:09
 */

namespace app\app\controller;
use app\app\controller\AppBase;

class Login extends AppBase
{
    /**
     * 用户注册
     */
    public function register(){
       $data     = input();
        $return['code'] = 100;
        $return['msg'] = '';
        $account  = check_mobile($data['account']);//验证手机号码
        if($account){
            $code  =  check_code($data);//验证验证码
            if($code){
                $data['token'] = settoken();//生成TOKEN值
                $bool  = model('members')->register($data);
                if($bool >0){
                    $info = model('members')->where(['account'=>$data['account']])->field('member_id,device_number,account,token')->find();
                    //控制表将以前有控制权的用户添上id
                    $kongzhi   =  model('productUser')->save(['member_id'=>$info['member_id']],['account'=>$info['account']]);
                    $return['code'] = 200;
                    $return['msg']  = '恭喜您,注册成功!';
                    $return['data'] = $info;
                }else{
                    $return['msg'] = $bool;
                }
            }else{
                $return['msg'] = '验证码不正确';
            }
        }else{
            $return['msg'] = '手机号码不正确';
        }
        return json($return);
    }
    /**
     * 获取验证码
     */
    public function sms_code(){
        $data            = input();
        $bool            = check_mobile($data['account']);//验证手机号码
        $return['code'] = 100;
        $return['msg']  = '';
        if($bool){
            //正式运行部分
            $bool = send_cms($data);//获取验证码
            if($bool === true){
                $return['code'] = 200;
                $return['msg'] = '验证码发送成功!';
            }elseif($bool != ""){
                $return['msg'] = $bool;
            }else{
                $return['msg'] = '获取频繁，请稍后再试!';
            }
            //正式运行部分
        }else{
            $return['msg'] = '手机号码不正确!';
        }
        return json($return);
    }

    /**
     * 用户登录
     */
    public function login(){
        $data               = input();
        $account            = check_mobile($data['account']);//验证手机号码
        $return['code'] = 100;
        $return['msg']  = '';
        if($account){
            $bool  = model('members')->login($data);
            if(isset($bool['member_id'])){
                $data['token'] = settoken();//生成TOKEN值
                $bool1  = model('members')->save(['token'=>$data['token']],['member_id'=>$bool['member_id']]);
                if($bool1 >0){
                    $bool['token'] = $data['token'] ;
                    session('member',$bool);
                    $return['code'] = 200;
                    $return['msg']  = '登录成功!';
                    $return['data'] = $bool;
                }

            }else{
                $return['msg']  = $bool;
            }
        }else{
            $return['msg']  = '账号不正确!';
        }
        return  json($return);
    }

    /**
     * 退出登录
     */
    public function out(){
        $data = input();
        session('member',[]);
        $data['token'] = settoken();//生成TOKEN值
        $bool  = model('members')->save(['token'=>$data['token']],['member_id'=>$data['member_id']]);
        $return['code'] = 100;
        $return['msg'] = '';
        if($bool > 0){
            $return['code'] = 200;
            $return['msg'] = '退出成功!';
        }else{
            $return['msg'] = '退出失败!';
        }
        return json($return);
    }

    /**
     * 第三方登录
     */
    public function thirdParty(){
        $data     = input();
        $return['code'] = 100;
        $return['msg'] = '';
        if(isset($data['openid'])){
            $token = settoken();//生成TOKEN值
            $ID  = model('members')->where(['account'=>$data['openid']])->field('member_id')->find();//检测是否有注册
            if(isset($ID['member_id'])){//有注册过
                if(isset($data['image'])){
                    $info['image']             = $data['image'];//头像
                }
                if(isset($data['sex'])){
                    $info['sex']                = $data['sex'];//性别 1男2女
                }
                if(isset($data['system'])){
                    $info['system']             = $data['system'];//操作系统
                }
                if(isset($data['membername'])){
                     $info['membername']             = $data['membername'];//昵称
                }
                if(isset($data['device_number'])){
                    $info['device_number']    = $data['device_number'];//设备号
                }
                if(isset($data['age'])){//年龄跟年龄段
                    $year                     = date('Y',time());
                    $age                      = (int)$year-(int)$data['age'];//出生年
                    $info['period']          = substr($age,2,1);//年龄段
                    $info['age']             = $data['age'];
                }
                $info['account']            = $data['openid'];//openid
                $info['token']              = $token;//token
                $info['last_time']          = time();//最后登录时间
                $info['today']              = mktime(0,0,0,date('m'),date('d'),date('Y'));//今天开始时间戳
                $w                           = date('w')? date('w'):7;
                $info['weeks']              = mktime(0,0,0,date('m'),date('d')-$w+1,date('Y'));
                $info['month_time']         = mktime(0,0,0,date('m'),1,date('Y'));//本月初时间戳
                $info['today_period']      = get_houer();//时间段
                $bool                        = model('members')->where(['member_id'=> $ID['member_id']])->update($info);
                if($bool >0){
                    $return['code'] = 200;
                    $return['msg']  = '登录成功!';
                    $return['data'] = [
                        'member_id'      => $ID['member_id'],
                        'token'          =>  $token,
                        'account'        => $data['openid'],
                        'device_number' => $data['device_number'],
                        'image'          => $data['image'],
                        'sex'            => $data['sex'],
                        'age'            => $data['age']
                    ];
                }else{
                    $return['msg'] = '登录失败!';
                }
            }else{//没有注册过
                if(isset($data['image'])){
                    $info['image']             = $data['image'];//头像
                }
                if(isset($data['sex'])){
                    $info['sex']                = $data['sex'];//性别 1男2女
                }
                if(isset($data['system'])){
                    $info['system']             = $data['system'];//操作系统
                }
                 if(isset($data['device_number'])){
                     $info['device_number']    = $data['device_number'];//设备号
                 }
                if(isset($data['membername'])){
                    $info['membername']             = $data['membername'];//昵称
                }
                if(isset($data['age'])){//年龄跟年龄段
                    $year                    = date('Y',time());
                    $age                     = (int)$year-(int)$data['age'];//出生年
                    $info['period']         = substr($age,2,1);//年龄段
                    $info['age']            = $data['age'];
                }
                $info['account']           = $data['openid'];//openid
                $info['token']             = $token;//token
                $info['last_time']         = time();//最后登录时间
                $info['today']             = mktime(0,0,0,date('m'),date('d'),date('Y'));//今天开始时间戳
                $w                          = date('w')? date('w'):7;
                $info['weeks']             = mktime(0,0,0,date('m'),date('d')-$w+1,date('Y'));
                $info['month_time']        = mktime(0,0,0,date('m'),1,date('Y'));//本月初时间戳
                $info['today_period']      = get_houer();//时间段
                $bool                        = model('members')->insertGetId($info);
                if($bool >0){
                    $return['code'] = 200;
                    $return['msg']  = '登录成功!';
                    $return['data'] = [
                        'member_id' => $bool,
                        'token'     => $token,
                        'account'   => $data['openid'],
                        'device_number' => $data['device_number'],
                        'image'  => $data['image'],
                        'sex'  => $data['sex'],
                        'age'  => $data['age'],
                    ];
                }else{
                    $return['msg'] = '登录失败!';
                }
            }
        }else{
            $return['msg']  = '没有获取到用户openid!';
        }
        return  json($return);
    }






}