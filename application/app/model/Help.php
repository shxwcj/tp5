<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/4/12
 * Time: 11:06
 */

namespace app\app\model;


use think\Model;
use traits\model\SoftDelete;

class Help extends Model
{
use SoftDelete;

    /**
     * 帮助列表
     * @param $data
     */
    public function getList($data){
        $list = $this->where('')->field('title,content')->select();
        return $list;
    }
}