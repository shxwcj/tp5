<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/3/31
 * Time: 12:29
 */

namespace app\app\model;


use think\Model;

class Sms extends Model
{
    /**
     * 添加短信
     * @param $data
     */
    public function addCode($data){
        $info = [];
        $time  = time();
        $info['account']  = $data['account'];//账号
        $info['code']     = $data['code'];//验证码
        $info['overdue_time']     = $time+70;//过期时间
        $this->where(['overdue_time'=>['<',$time]])->whereOr(['account'=>$info['account']])->delete(true);//删除过期的验证码
        $bool = $this->save($info);//添加信息
        return $bool;
    }
    /**
     * 验证验证码
     * @param $obj
     * @return  bool
     */
    public function getCode($data){
        $overdue_time  = time();
        $where['account'] =   $data['account'];
        if(!isValue($data,'sms_code')) return '填写验证码!';
        $where['code']    = $data['sms_code'];
        $where['overdue_time']    = ['>',$overdue_time];
        $bool  = $this->where($where)->field('id')->find();
        if(isset($bool['id'])){
            return  $bool['id'];
        }
        return  false;
    }
}