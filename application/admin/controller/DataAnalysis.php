<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/4/13
 * Time: 15:11
 */

namespace app\admin\controller;


class DataAnalysis extends AdminBase
{
    /*
     * 数据分析
     */
    public function index(){
        //$this->systemAnalysis();
        return view();
    }

    /**
     * 年龄分析
     * @return \think\response\Json
     */
    public function ageAnalysis(){
        //查询年龄单
        $info = model('members')->getAgeData();
        return json($info);
    }
    /**
     * 系统分析
     * @return \think\response\Json
     */
    public function systemAnalysis(){
        //查询年龄单
        $info = model('members')->getSystemData();
        return json($info);
    }
    /**
     * 性别分析
     * @return \think\response\Json
     */
    public function sexAnalysis(){
        //查询年龄单
        $info = model('members')->getSexData();
        return json($info);
    }

    /**
     * 地区分析
     * @return \think\response\Json
     */
    public function areas(){
        $arr = [ '北京'=>0,'天津'=>0,'上海'=>0,'重庆'=>0,'河北'=>0,'河南'=>0,'云南'=>0,'辽宁'=>0,'黑龙江'=>0,'湖南'=>0,'安徽'=>0,'山东'=>0,
                 '新疆'=>0,'江苏'=>0,'浙江'=>0,'江西'=>0,'湖北'=>0,'广西'=>0,'甘肃'=>0,'山西'=>0,'内蒙古'=>0,'陕西'=>0,'吉林'=>0,'福建'=>0,
                 '贵州'=>0,'广东'=>0,'青海'=>0,'西藏'=>0,'四川'=>0,'宁夏'=>0,'海南'=>0,'台湾'=>0,'香港'=>0,'澳门'=>0,'min'=>10,'all'=>0];
        $list = model('Product')->where('')->column('areas');
        foreach ($list as $k => $v ){
            $name = substr($v,0,9);
            if(isset($arr[$name])){
                $arr[$name] ++;
            }else{
                $name1 = substr($v,0,6);
                if(isset($arr[$name1])){
                    $arr[$name1] ++;
                }
            }
        }
        foreach($arr as $a=>$b){
            if($b>$arr['min']){
                $arr['min']=$b;
            }
        }
        $arr['all'] = array_sum($arr)-10;
        return json($arr);
    }

}