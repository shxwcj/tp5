<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/4/16
 * Time: 11:34
 */

namespace app\common\model;


use think\Model;
use traits\model\SoftDelete;

/**
 *APP轮播图
 * Class Slideshow
 * @package app\common\model
 */
class Slideshow  extends Model
{
    use SoftDelete;
    /**
     * 获取轮播图列表
     */
    public function  getList($data){
        $where = '';
        if(isValue($data,'img_name')){
            $where['img_name'] =['like','%'.(string)$data['img_name'].'%'];
        }
        $list = $this->where($where)->paginate(15,false,['query'=>$data]);
        return $list;

    }

    /**
     * App获取轮播图
     * @param $data
     */
    public function getAppList($data){
        $where['status'] = 1;
        $list = $this->where($where)->order('create_time desc')->column('path');
        if (!empty($list)){
            foreach ($list as $k=>$v){
               $bool =  check_image($v);
               if(!$bool){
                  unset($list[$k]) ;
               }
            }
        }
        return $list;
    }


    /**
     * 添加轮播图
     * @param $data
     */
    public function addSlideShow($data){
        $info['img_name']  = $data['img_name'];
        $info['path']  = $data['path'];
        return $this->save($info);
    }

    /**
     * APP轮播图禁用/启用
     * @param $data
     */
    public function  change_status($data){
         return $this->save(['status'=>$data['status']],['img_id'=>$data['img_id']]);
    }
    /**
     * 单个删除轮播图
     * @param $data
     */
    public  function del($data){
        $img  = $this->where(['img_id'=>$data['id']])->column('path');
        deleteImgAll($img);
        return  $this->where(['img_id'=>$data['id']])->delete(true);
    }

    /**
     * 批量删除轮播图
     * @param $data
     */
    public  function delAll($data){
        $img  = $this->where(['img_id'=>['in',$data['id']]])->column('path');
        deleteImgAll($img);
        return  $this->where(['img_id'=>['in',$data['id']]])->delete(true);
    }


}