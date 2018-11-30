<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/3/12
 * Time: 15:33
 */

namespace app\admin\controller;
use app\admin\controller\AdminBase;
use app\admin\Model\Admin;
use think\Request;
use think\Image;
use think\Session;
use extend\Encrypt;
/**
 * 会员管理类
 * Created by PhpStorm.
 * @author pengqing
 * User: THINK
 * Date: 2018/3/12
 * Time: 15:37
 */

class Members extends AdminBase
{
    /**
     * 用户列表
     */
    public function index(){
        $data   = input();
        $list  = model('members')->getList($data);
       return view([
           'list' => $list,
       ]);
    }

    /**
     * 修改用户
     */
    public function edit(){
        $data   = input();
        if(request()->isAjax()){
            $bool   = model('members')->edit($data);
            if ($bool>0) {
                Api()->setApi('url', url('Members/index', ['page' => input('page')]))->ApiSuccess();
            } else {
                Api()->setApi('msg',$bool)->setApi('url',0)->ApiError();
            }
        }else{
            $info  = model('members')->getInfo($data);//获取用户详情
            $areas                  = model('areas')->getAreas($info);//省市区列表
            return view([
                'info'   => $info,
                'areas'  => $areas,
                'page'   => input('page'),
                ]);
        }
    }
    /**
     * 单个删除用户
     */
    public function del(){
        $data = input();
        $bool = model('Members')->del($data);
        if ($bool) {
            Api()->setApi('url', url('Members/index', ['page' => input('page')]))->ApiSuccess();
        } else {
            Api()->ApiError();
        }
    }

    /**
     * 批量删除用户
     */
    public function Alldel(){
        $data = input();
        $bool = model('Members')->Alldel($data);
        if ($bool) {
            Api()->setApi('url', url('Members/index', ['page' => input('page')]))->ApiSuccess();
        } else {
            Api()->ApiError();
        }
    }




    /**
     * 账号/密码管理
     * @author pengqing
     */
    public function  account ()
    {
        echo '这里是--------账号/密码管理';
    }
    /**
     * 登录日志管理
     * @author pengqing
     */
    public function  loginLog ()
    {
        echo '这里是--------登录日志管理';
    }
    /**
     * 获取新增设备的数据(当天/本周/本月)
     */
    public function cut(){
        $type   =   input()?input('type'):'today';
        $data   = model('Members')->getCut($type);
        return json($data);
    }

    /**
     * 用户详情
     */
    public function info(){
        $info  = model('members')->where('member_id',input('member_id'))->find();
        $info['product_id']  = input('product_id');
       return view(['info'=>$info]);

    }
}