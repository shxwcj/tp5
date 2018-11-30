<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/4/9
 * Time: 11:34
 */

namespace app\app\model;


use think\Model;

class Members extends Model
{
    /**
     * 用户注册
     * @param $data
     */
    public function register($data){
        if(!isValue($data,'password'))  return '填写密码!';
        $data['password']       = trim($data['password']);
        $info['account']        = $data['account'];
        $info['password']       = md5($data['password']);
        $info['token']          = $data['token'];
        $info['system']         = $data['system'];
        if(!isValue($data,'areas')){
            $info['areas']      = $data['areas'];
        }
        $info['last_time']      = time();
        $info['today']          = mktime(0,0,0,date('m'),date('d'),date('Y'));//今天开始时间戳
        $w                       = date('w')? date('w'):7;
        $info['weeks']          = mktime(0,0,0,date('m'),date('d')-$w+1,date('Y'));
        $info['month_time']     = mktime(0,0,0,date('m'),1,date('Y'));//本月初时间戳
        $info['today_period']   =get_houer();
        $bool                    = $this->validate('members.add')->save($info);
        if($bool === false){
            return $this->getError();
        }else{
            return $bool;
        }
    }

    /**
     * 用户登录
     * @param $data
     */
    public function login($data){
        if(!isValue($data,'password')) return '填写密码!';
        $bool = $this->where(['account'=>$data['account']])->field('member_id,device_number,account,password,token,sex,image,year,month,day')->find();
        if(isset($bool['password'])){
            $pass= md5($data['password']);
            if($bool['password'] == $pass){
                $new['last_time'] = time();
                $new['system']          = $data['system'];
                if($data['device_number'] != $bool['device_number']){//设备号发生变化
//                    $token  = settoken();
//                    $new['token'] = $token;
//                    $new['device_number'] = $data['device_number'];
//                    $bool['token'] = $token;
                }
                $this->save($new,['member_id'=>$bool['member_id']]);
                return $bool;
            }else{
                return '密码错误!';
            }
        }else{
            return '账号不存在!';
        }
    }

    /**
     * 用户忘记密码
     * @param $data
     */
    public function edit_password($data){
        if(!isValue($data,'password')) return '填写密码!';
        $data['password'] = trim($data['password']);
        $info = $this->where(['account'=>$data['account']])->field('member_id,account,password,token,sex,image,year,month,day')->find();

        if(isset($info['member_id'])){
            $new['password'] = md5($data['password']);
            $new['last_time'] = time();
           $bool =  $this->save($new,['member_id'=>$info['member_id']]);
           if($bool >0){
               return $info;
           }else{
               return '修改失败!';
           }
        }else{
            return '账号不存在!';
        }
    }

    /**
     * 用户修改密码
     * @param $data
     */
    public function editPassword($data){
        $info  = $this->where(['account'=>$data['account']])->field('member_id,password')->find();
        $password = md5($data['password']);
        $new_password = md5($data['new_password1']);
        if($info === NULL){
            return  '账号不存在!';
        }else{
            if($password == $info['password']){
                $bool = $this->save(['password'=>$new_password],['member_id'=>$info['member_id']]);
                if($bool>0){
                    return  $bool;
                }else{
                    return  '密码修改失败!';
                }
            }else{
                return  '原密码不正确!';
            }
        }
    }

    /**
     * 修改昵称
     * @param $data
     */
    public function editName($data){
        $bool = $this->save(['membername'=>$data['membername']],['member_id'=>$data['member_id']]);
        return $bool;
    }

    /**
     * 修改性别
     * @param $data
     */
    public function editSex($data){
        $bool = $this->save(['sex'=>$data['sex']],['member_id'=>$data['member_id']]);
        return $bool;
    }
    /**
     * 修改年龄
     * @param $data
     */
    public function editAge($data){
        $year   = date('Y',time());
        $age = (int)$year-(int)$data['age'];//出生年
        $period = substr($age,2,1);//年龄段
        $bool = $this->save(['age'=>$data['age'],'period'=>$period],['member_id'=>$data['member_id']]);
        return $bool;
    }

    /**
     * 用户详情
     * @param $data
     */
    public function memberInfo($data){
        $info = $this->where(['member_id'=>$data['member_id']])->field('member_id,age,account,membername,sex,image')->find();
        return $info;
    }

}