<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/4/12
 * Time: 11:06
 */

namespace app\common\model;


use think\Model;
use traits\model\SoftDelete;

class Help extends Model
{
use SoftDelete;
    /**
     * PC帮助列表
     * @param $data
     */
    public function getListPc($data){
        $where  = '';
        if(isValue($data,'start_time') && isValue($data,'end_time')){
            $start = strtotime($data['start_time']);
            $end = strtotime($data['end_time']);
            $where['create_time']    = ['BETWEEN',[$start,$end]];
        }else{
            if(isValue($data,'start_time')){
                $start = strtotime($data['start_time']);
                $where['create_time'] = ['>=',$start];
            }
            if(isValue($data,'end_time')){
                $end = strtotime($data['end_time']);
                $where['create_time'] = ['<=',$end];
            }
        }
        if(isValue($data,'page')){
            $page = $data['page'];
        }else{
            $page = 1;
        }
        $list = $this->where($where)->paginate(10,false,['query'=>$data]);
        return $list;
    }
    /**
     * 帮助列表
     * @param $data
     */
    public function getList($data){
        $list = $this->where('')->field('title,content')->select();
        return $list;
    }

    /**
     * 帮助详情
     * @param $data
     */
    public function info($data){
        $info = $this->where(['help_id'=>$data['help_id']])->find();
        return $info;
    }
    /**
     * 帮助添加
     * @param $data
     */
    public function add($data){
        if(!isValue($data,'title'))   return '填写标题!';
        $content  = trim($data['content']);
        if(empty($content)) return '填写内容!';
        $new['title']= $data['title'];
        $new['content']= $data['content'];
        $bool = $this->save($new);
        return $bool;

    }
    /**
     * 帮助编辑
     * @param $data
     */
    public function edit($data){
        if(!isValue($data,'title'))   return '填写标题!';
        if(!isValue($data,'content')) return '填写内容!';
        $new['title']= $data['title'];
        $new['content']= $data['content'];
        $bool = $this->save($new,['help_id'=>$data['help_id']]);
        return $bool;

    }
    /**
     * 单个帮助删除
     * @param $data
     */
    public function del($data){
        $bool  = $this->where(['help_id'=>$data['id']])->delete(true);
        return $bool;
    }
    /**
     * 批量帮助删除
     * @param $data
     */
    public function delAll($data){
        $bool  = $this->where(['help_id'=>['in',$data['id']]])->delete(true);
        return $bool;
    }
}