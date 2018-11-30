<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/4/12
 * Time: 10:49
 */

namespace app\app\model;
use think\Model;
use traits\model\SoftDelete;

class Feedback extends  Model
{
use  SoftDelete;

    /**
     * 意见列表
     * @param $data
     */
    public  function getList($data){
        $where  = '';
        if(isValue($data,'start_time') && isValue($data,'end_time')){
            $start = strtotime($data['start_time']);
            $end = strtotime($data['end_time']);
            $where['p.create_time']    = ['BETWEEN',[$start,$end]];
        }else{
            if(isValue($data,'start_time')){
                $start = strtotime($data['start_time']);
                $where['p.create_time'] = ['>=',$start];
            }
            if(isValue($data,'end_time')){
                $end = strtotime($data['end_time']);
                $where['p.create_time'] = ['<=',$end];
            }
        }
        if(isValue($data,'page')){
            $page = $data['page'];
        }else{
            $page = 1;
        }
        $list = $this->alias('p')->join('members m','p.member_id=m.member_id')
            ->where($where)->field('p.*,m.account')->paginate(10,false,['query'=>$data]);
            return $list;
    }
    /**
     * 添加反馈意见
     * @param $data
     */
     public function addFeedback($data){
         if(!isValue($data,'content')) return  '填写反馈内容!';
        $feedback  = trim($data['content']);
        if(empty($feedback)) return '填写反馈内容!';
        $lengs     = strlen($feedback);
        if($lengs > 430) return '反馈内容不得超过140字!';
         $new['member_id'] = $data['member_id'];
         $new['content'] = $data['content'];
        $bool  = $this->save($new);
        return $bool;
     }

    /**
     * 意见详情
     * @param $data
     */
     public function info($data){
         $info  = $this->alias('f')->join('xi_members m','f.member_id=m.member_id')
                       ->where(['f.feedback_id'=>$data['feedback_id']])->field('f.*,m.account')->find();
         return  $info;
     }

    /**
     * 单个意见删除
     * @param $data
     */
    public function del($data){
        $bool  = $this->where(['feedback_id'=>$data['id']])->delete(true);
        return $bool;
    }
    /**
     * 批量意见删除
     * @param $data
     */
    public function delAll($data){
        $bool  = $this->where(['feedback_id'=>['in',$data['id']]])->delete(true);
        return $bool;
    }
}