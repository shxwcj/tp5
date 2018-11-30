<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/4/21
 * Time: 15:41
 */

namespace app\common\model;


use think\Model;
use traits\model\SoftDelete;

class Push extends Model
{
use SoftDelete;

    /**
     * App消息列表
     * @param $data
     */
    public function getMessage($data){
        $list = $this->where(['member_id'=>$data['member_id']])->field('id,content,create_time')->order('create_time desc')->select();
        return $list;
    }


    /**
     * 添加消息
     * @param $data
     */
    public function  addPush($data,$content){
        if($data == 'all'){
           $list  = model('members')->where('')->column('member_id');
        }elseif(isset($data['alias'])) {
            $where['jiguang_id']  =['in',$data['alias']];
            $list  = model('members')->where($where)->column('member_id');
        }else{
            $list = [$data];
        }
        $all  = [];
        foreach ($list as $k => $v){
            $all[$k]['member_id']  = $v;
            $all[$k]['content']  = $content;
        }
       return  $this->saveAll($all);
    }

    /**
     * 删除消息
     * @param $data
     */
    public function delMessage($data){
         if(!is_array($data['id'])) return 'id必须是数组形式';
         $bool  = $this->where(['id'=>['in',$data['id']]])->delete(true);
         return $bool;
    }









}