<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/4/16
 * Time: 16:49
 */

namespace app\admin\model;


use think\Model;

class About  extends Model
{
    public function add($data){
        if (isValue($data,'path')){
            $info['image']  = $data['path'];
        }
        $info['describe']  = $data['describe'];
        $bool = $this->save($info,['id'=>$data['id']]);
        return $bool;
    }
}