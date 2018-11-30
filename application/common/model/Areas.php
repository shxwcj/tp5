<?php
/**
 * Created by PhpStorm.
 * @author  pengqiang
 * User: THINK
 * Date: 2018/3/13
 * Time: 13:49
 */

namespace app\common\model;


use think\Model;

class Areas extends Model
{
    /**
     * 获取省市区
     * $data   array
     */
    public function getAreas($data)
    {
        $pid = [1, $data['province'], $data['city']];
        $list = $this->where(['parent_id'=>['in',$pid]])->field('area_id, parent_id, area_name')->select();
        $newList=array();
        foreach ($list as $k => $v){
            if($v['parent_id'] == 1){
                $newList['province'][]=$v;
            }else if($v['parent_id'] == $data['province']){
                $newList['city'][]=$v;
            }else if($v['parent_id'] == $data['city']){
                $newList['area'][]=$v;
            }
        }
        return $newList;
    }

    /**
     * 通过area_id获取下级城市
     * @param $areaId  int 上级area_id
     * return array
     */
    public function getGunior($areaId)
    {
      $list = $this->where(['parent_id' => $areaId])->select();
      return $list;
    }


}