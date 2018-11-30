<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/4/17
 * Time: 16:28
 */

namespace app\app\model;
use think\Model;

class About extends  Model
{
    public function getAbout($data){
        $info  = $this->where('')->field('image,describe')->find();
        if(!empty($info)){
            $bool = check_image($info['image']);
            if(!$bool){
                $info['image'] = null;
            }
        }
        return $info;
    }
}