<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/4/11
 * Time: 15:34
 */

namespace app\app\model;


use think\Model;
use traits\model\SoftDelete;

class Group extends Model
{
    use SoftDelete;
    /**
     * 获取组合列表
     * @param $data
     */
    public function getList($data){
        if(!isValue($data,'member_id')) return '没有获取到用户信息!';
        $list    = $this->where(['member_id'=>$data['member_id']])->field('group_id,group_name,num,product_num')->select();
        $new = model('product')->getList($data);
        if(!empty($list)){
            foreach ($list as $k => &$v){
                $arr = unserialize($v['product_num']);
                $v['product_num'] =[];
                $aa=[];
                foreach ($new as $a =>$b)  {
                    if(in_array($b['product_id'],$arr)){
                        $aa[] = $new[$a];
                    }
                }
                $v['product_num'] = $aa;//$new[$a];
            }
            return $list;
        }else{
            return null;
        }
    }

    /**
     * 添加设备组合
     * @param $data
     * @return false|int|string
     */
    public function addMerge($data){
        if(!isValue($data,'group_name')) return '填写组合名称!';
        if(!is_array($data['product_num'])) return '选择设备!';
        $product_num = serialize($data['product_num']);
      /*  $where['product_num'] = $product_num;
        $where['member_id'] = $data['member_id'];
        $bool = $this->where($where)->field('group_id')->find();//判断该用户有没有该组合
        if(isset($bool['group_id'])) return '该组合已存在!';*/
        $info['product_num'] = $product_num;
        $info['group_name']  = $data['group_name'];
        $info['member_id']  = $data['member_id'];
        $info['num']  = count($data['product_num']);
        $bool  = $this->save($info);
         return $bool;
    }

    /**
     * 组合详情
     * @param $data
     */
    public function groupInfo($data){
        if(!isValue($data,'group_id')) return '没有获取到设备组合信息!';
        $arr  = $this->where(['group_id'=>$data['group_id']])->field('product_num')->find();
        $arr = unserialize($arr['product_num']);
        $list = model('product')->where(['product_id'=>['in',$arr]])->field('product_id,product_num,product_name,online_status,temperature,networking,protocol_type,type,mac_areas')->select();
        if(!empty($list)){
            foreach ($list as $k =>&$v){
                $v['group_id'] = $data['group_id'];
            }
           return $list;
       }else{
           return null;
       }
    }

    /**
     * 组合设备详情
     * @param $data
     */
    public  function checkedProduct($data){
        if(!isValue($data,'group_id')) return '没有获取到设备组合信息!';
        $arr  = $this->where(['group_id'=>$data['group_id']])->field('product_num')->find();
        $arr = unserialize($arr['product_num']);
        if(!empty($arr)){
            return $arr;
        }else{
            return null;
        }
    }

    /**
     * 删除设备组中的某台设备
     * @param $data
     */
    public function delProduct($data){
        $arr  = $this->where(['group_id'=>$data['group_id']])->field('product_num')->find();
        $arr  = unserialize($arr['product_num']);
        array_splice($arr, array_search($data['product_num'],$arr), 1);//删除数组中的元素,生成新的索引.
        $num  = count($arr);
        if($num){//还有设备就保留
            $arr  = serialize($arr);
            $bool = $this->save(['product_num'=>$arr,'num'=>$num],['group_id'=>$data['group_id']]);
        }else{
            $bool = $this->where(['group_id'=>$data['group_id']])->delete(true);
        }
        return $bool;
    }

    /**
     * 设备组中添加设备
     * @param $data
     */
    public function addProduct($data){
        $arr  = $this->where(['group_id'=>$data['group_id']])->field('product_num')->find();
        $arr  = unserialize($arr['product_num']);
        if(!is_array($data['product_num'])) return '添加的设备有误!';
        foreach ($data['product_num'] as $k => $v){
            array_push($arr,$v);
        }
        $num = count($arr);
        $arr = serialize($arr);
        $bool = $this->save(['product_num'=>$arr,'num'=>$num],['group_id'=>$data['group_id']]);
        if($bool >0){
         $list = $this->groupInfo($data);
         return $list;
        }else{
            return '添加失败!';
        }
        return $bool;
    }

    /**
     * 修改名称
     * @param $data
     */
    public function editName($data){
        if(!isValue($data,'group_name')) return '填写新名称';
        $bool = $this->save(['group_name'=>$data['group_name']],['group_id'=>$data['group_id']]);
        return $bool;
    }

    /**
     * 删除组合
     * @param $data
     */
    public function delMerge($data){
        $bool = $this ->where(['group_id' =>$data['group_id']])->delete(true);
        return $bool;
    }
}