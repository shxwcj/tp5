<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/4/8
 * Time: 11:26
 */

namespace app\admin\model;


use think\Model;
use traits\model\SoftDelete;

class Members  extends Model
{
use SoftDelete;

    /**
     * 获取用户列表
     * @param $data
     */
    public function getList($data){
        $where  = '';
        if(isValue($data,'account')){
            $where['account'] = $data['account'];
        }
        $list   = $this -> where($where)->field('member_id, account, sex, type, membername, last_time, status')
            ->paginate(15,false,['query'=>$data]);
        return $list;



    }

    /***
     * 获取用户详情
     * @param $data
     */
    public function getInfo($data){
         if(!isValue($data,'member_id'))  return false;
         $info   = $this ->where(['member_id'=>$data['member_id']])->find();
         return $info;
    }

    /**
     * 修改会员资料
     * @param $data
     */
    public  function edit($data){
        $name   = mb_strlen($data['membername'], 'UTF-8') . "";
        if($name == 0  || $name > 8){
            return '昵称长度应该在1-8个字符之间!';
        }
        if(preg_match("/^[0-9][0-9]*$/",$data['age'])){
            if($data['age'] > 100){
                return '年龄应该在0-100之间!';
            }
        }else{
            return '年龄应该是整数!';
        }


        unset($data['page']);
        $year   = date('Y',time());
        $age = (int)$year-(int)$data['age'];//出生年
        $data['period'] = substr($age,2,1);//年龄段
       $bool  = $this->save($data,['member_id'=>$data['member_id']]);
       return $bool;

    }

    /**
     * 批量删除用户
     * @param $data
     */
    public function Alldel($data){
        $bool  = $this->where(['member_id'=>['in',$data['id']]])->delete(true);
        return $bool;
    }
    /**
     * 单个删除用户
     * @param $data
     */
    public function del($data){
        $bool  = $this->where(['member_id'=>$data['id']])->delete(true);
        return $bool;
    }
    /***
     * 获取不同时间段的新增用户量
     * @param $data
     */
    public function getCut($data){
        if($data == 'today' || $data == 'weeks' || $data == 'month'){
            if($data == 'today' ){
                $time    =   mktime(0,0,0,date('m'),date('d'),date('Y'));//今天开始时间戳
                $where['today']  = $time;
                $field = 'today_period,member_id';
            }else if($data == 'weeks'){
                $w       = date('w')? date('w'):7;
                $time    = mktime(0,0,0,date('m'),date('d')-$w+1,date('Y'));//本周时间戳
                $where['weeks']  = $time;
                $field = 'today,member_id';
            }else if($data == 'month'){
                $time    = mktime(0,0,0,date('m'),1,date('Y'));//本月初时间戳
                $where['month_time']  = $time;
                $field = 'today,member_id';
            }
            $list = $this ->where($where)->field($field)->select();
            $tree   = $this-> tongji($data,$list);
            return $tree;
        }
        return  false;

    }

    /**
     * 数据统计
     * @param $data
     */
    protected  function tongji($data,$list){
        if($data == 'today' ){
            $type  = ['0~4','4~8','8~12','12~16','16~20','20~0'];
            $info  = [0,0,0,0,0,0];
            foreach ($list as $k => $v){
                $info[$v['today_period']]++;
            }
        }else if($data == 'weeks'){
            $type =['星期天','星期一','星期二','星期三','星期四','星期五','星期六'];
            $weeks3 =[];
            $info = [0,0,0,0,0,0,0];
            $sunday  =  strtotime( "previous sunday");
            for ($b=0;$b<7;$b++){
                $time  = $sunday + $b*(24*60*60);
                $weeks3[$type[$b]]  = $time ;
            }
            foreach ($list as $ke => $va ){
                $dd  =  array_search($va['today'],$weeks3);
                $dd  =  array_search($dd,$type);
                $info[$dd]++;
            }
        }else if($data == 'month'){
            $year  = date('Y');//当前年份
            $month = date('m');//当前月份
            $timn  = date('d');//当天号数
            $type = [];
            $type1 = [];
            $info = [];
            for ($i=1;$i<=$timn;$i++){
                $strtime   = strtotime($year."-".$month."-".$i);
                $type1[$i-1]  =  $strtime;
                $type[$i-1]     = $i;
                $info[$i-1] = 0;
            }

            foreach ($list as $key => $val ){
                $day =  array_search($val['today'],$type1);
                $info[$day] += 1;
            }
        }
        $nenList['type'] = $type;
        $nenList['info'] = $info;
        return  $nenList;
    }
    /***
     * 获取不同年龄段的新增用户量
     * @param $data
     */
    public function getAgeData(){
            $list = $this->field('period')
                ->select();
            $dataTree  = $this->tongjiAge($list);
            return $dataTree;
    }

    /**
     * 数据统计
     * @param $data
     */
    protected  function tongjiAge($list){
            $type  = ['50后','60后','70后','80后','90后','00后','10后'];
            $info  = [0,0,0,0,0,0,0];
            foreach ($list as $k => $v){
                if($v['period']==5){
                    $info[0]++;
                    continue;
                }
                if($v['period']==6){
                    $info[1]++;
                    continue;
                }
                if($v['period']==7){
                    $info[2]++;
                    continue;
                }
                if($v['period']==8){
                    $info[3]++;
                    continue;
                }
                if($v['period']==9){
                    $info[4]++;
                    continue;
                }
                if($v['period']==0){
                    $info[5]++;
                    continue;
                }
                if($v['period']==1){
                    $info[6]++;
                    continue;
                }
            }
        $nenList['type'] = $type;
        $nenList['info'] = $info;
        return  $nenList;
    }
    /***
     * 获取不同系统的新增用户量
     * @param $data
     */
    public function getSystemData(){
        $list = $this->field('system')
            ->select();
        $dataTree  = $this->tongjiSystem($list);
        return $dataTree;
    }

    /**
     * 数据统计
     * @param $data
     */
    protected  function tongjiSystem($list){
        $type  = ['Android','iOS'];
        $info  = [0,0];
        foreach ($list as $k => $v){
            if($v['system']=='Android'){
                $info[0]++;
                continue;
            }
            if($v['system']=='iOS'){
                $info[1]++;
                continue;
            }
        }
        //$nenList['type'] = $type;
        $nenList['info'] = $info;
        return  $nenList;
    }
    /***
     * 获取不同性别的新增用户量
     * @param $data
     */
    public function getSexData(){
        $list = $this->field('sex')
            ->select();
        $dataTree  = $this->tongjiSex($list);
        return $dataTree;
    }

    /**
     * 数据统计
     * @param $data
     */
    protected  function tongjiSex($list){
        $type  = ['男','女'];
        $info  = [0,0];
        foreach ($list as $k => $v){
            if($v['sex']==1){
                $info[0]++;
                continue;
            }
            if($v['sex']==2){
                $info[1]++;
                continue;
            }
        }
        //$nenList['type'] = $type;
        $nenList['info'] = $info;
        return  $nenList;
    }

    /**
     * 消息推送(获取用户的极光ID)
     * @param $data
     */
    public function getJiGuang($data){
        if(empty($data['content'])) return '填写消息内容!';
        $where  = '';
        //年龄
        if(isValue($data,'since')){
            if($data['since'] > 0){
                if($data['check'] > 0){
                    $where['age'] = ['BETWEEN',[$data['since'],$data['check']]];
                }else{
                    $where['age'] = ['>=',$data['since']];
                }
            }
        }
        //操作系统
        if(isValue($data,'system')){
            if(isset($data['system'][1]) && isset($data['system'][0])){}else{
                $where['system'] = $data['system'][0];
            }
        }
        //性别
        if(isValue($data,'sex')){
            if(isset($data['sex'][1]) && isset($data['sex'][0])){}else{
                $where['sex'] = $data['sex'][0];
            }
        }
        if(empty($where)){
            return 'all';
        }else{
            $list  = $this->where($where)->column('jiguang_id');
            return $list;
        }

    }


}