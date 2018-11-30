<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/4/8
 * Time: 14:26
 */

namespace app\app\controller;


use app\common\controller\Base;

class AppBase extends Base
{
    public function __construct(){
        parent::__construct();
    }
    public function _initialize(){
        parent::_initialize();
        $this->_initsys();
    }
    protected $allowed = array(
        'app'=>[
            'login.*',
        ],
    );

    final protected function _initsys(){
        $this->islogin();//是否登录和token是否一致
    }
    /**
     * 操作日志
     * @param string    $operator 被修改的用户名称
     * @param string    $type     日志的类型
     * @param string    $update   修改之前值
     * @param string    $updates  修改之后值
     */
    protected function operation_log($type,$update,$updates,$operator=''){
        $list = Db::name('members')->where('member_id',session('islogin'))->find();
        $ip = $_SERVER["REMOTE_ADDR"]; //用户id
        $location = getCity($ip)['region'].' '.getCity($ip)['isp']; //登录地址
        $time = time();//日志添加时间
        $data = ['c_time'=>$time,'j_ip'=>$ip,'location'=>$location,'update'=>$update,'updates'=>$updates,'member'=>$list['member_name'],'operator'=>$operator,'type'=>$type];
        // 启动事务
//        Db::startTrans();
//        try{
        $list = Db::name('journal')->insert($data);
//            // 提交事务
//            Db::commit();
//        } catch (\Exception $e) {
//            // 回滚事务
//            Db::rollback();
//        }
        return $list;
    }

    /**
     * 判断是否登录和token是否一致
     */
    protected function islogin($data = null){
        $module_name                                 = strtolower(MODULE_NAME);
        $controller_name                             = strtolower(CONTROLLER_NAME);
        $action_name                                 = strtolower(ACTION_NAME);
        if (array_key_exists($module_name, $this->allowed)) {
            $auth1                                   = $controller_name.'.*';
            $auth2                                   = $controller_name.'.'.$action_name;
            if (in_array($auth1, $this->allowed[$module_name])) {
                return;
            } elseif(in_array($auth2, $this->allowed[$module_name])) {
                return;
            }
        }
        //检测Token
        $data = '{"token":"78a51757182f4fe6d9752f42f14ee1d634a0117"}';
        $obj = json_decode($data,true);
        $token = model('members')->where(['token'=>$obj['token']])->field('member_id')->find();//判断token是否相等
        if(1 != $this->is_login()->code && !request()->isAjax() && !isset($token['member_id'])){
           // $this->redirect('publics/login');//重定向,跳登录页面
            return;
        }
    }

    /**
     * 判断token是否相等
     */
    public function is_equalToken($token){
        //判断token是否相等
        $token = model('member')->where(['token'=>$token])->field('id')->find();
        return $token;
    }
}