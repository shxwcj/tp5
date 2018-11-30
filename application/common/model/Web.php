<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/3/13
 * Time: 14:21
 */

namespace app\common\model;


use think\Model;

class Web extends Model
{
    /**
     * 获取网站基础信息
     */
    public function getWeb ()
    {
        $info = $this->where(['id'=>1])->find();
        return $info;
    }

    /**
     * 获取关于我们的内容
     * @param $data
     */
    public  function  getAbout($data){
        $content   = $this->where('')->field('describe')->find();
        return $content['describe'];
    }

}