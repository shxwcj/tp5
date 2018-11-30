<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/4/12
 * Time: 17:40
 */

namespace app\admin\controller;
use app\admin\controller\AdminBase;
use extend\Jpush;
class Massage extends  AdminBase
{
    /**
     * 推送消息
     */
    public function index(){
        if(request()->isAjax()){
            #########测试########
//            $jiguang  = new Jpush();
//            $content = '你看到消息了吗?';   //推送的内容。
//            $receive = array('registration_id'=>array('161a3797c8291799b70'));    //别名 设备标识
//           // $num['alias']=  array('42dd9a3eb20848c219dfabee8ea6f45a4185a16fb385bc9350c6cbb2cf2301ff');
//            $bool =   $jiguang->send_pub($receive,'阿红,好看不?','','','');
//           return  json($bool);
            #########测试########
            $data  = input();
            $members   = model('members')->getJiGuang($data);//获取符合条件用户的极光ID
            if($members == 'all'){
                 $guang = 'all';
            }elseif(is_array($members) && !empty($members)){
                $guang['alias'] = $members;
            }elseif(is_array($members) && empty($members)){
                $err['msg'] = '没有适合的用户!';
            }else{
                $err['msg'] = $members;
            }
            if(isset($guang)){//消息推送
                $content  = $data['content'];
                $jiguang  = new Jpush();
                $tui       =  $this->tuisong($guang,$content);  //添加消息
                $jiguang->send_pub($guang,$content);//消息推送
            }
            if(isset($err)){
                $status['code']  = 200;
                $status['msg']  = $err['msg'];
            }else{
                if(is_array($tui)){
                    $status['code']  = 100;
                }
            }
            return json($status);
        }
       return view();
    }

    /**
     * 推送消息
     * @param $data 极光id(数组)
     */
    public function tuisong($data,$content){
        $bool = model('push')->addPush($data,$content);
        return $bool;

    }

}