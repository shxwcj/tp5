<?php
namespace app\admin\controller;
use app\admin\controller\AdminBase;
use app\admin\Model\Admin;
use extend\Upload;
use think\Request;
use think\Image;
use think\Session;
use extend\Encrypt;
/**
 * 管理员控制器
 * @author  pengqiang
 * @version 2017/5/12
 */
class Admins extends AdminBase{
    public function defaluts(){
        $this->redirect(url('Admins/index'));
    }

    /**
     * 管理员列表
     */
    public function index ()
    {
        $authGroup =model('AuthGroup')->where('group_id != 1')->select();
        resultToArray($authGroup);
        $data=input();
        $admins = model('Admin')->select_admin($data);
        return view('',[
            'admins'=>$admins,'authGroup'=>$authGroup,
        ]);
    }

    /**
     * 编辑管理员
     */
    public function edit ()
    {
    	$authGroup =model('AuthGroup')->where('group_id != 1')->select();
    	resultToArray($authGroup);
    	$admin_id=input('admin_id');
    	$admin_info = Admin::get($admin_id)->toArray();
        $page =input('page');
    	if (request()->isAjax()) {
            $data=input();
           	$re =model('Admin')->edit_admin($data);
           	if($re >0){
           		Api()->setApi('url',url('Admins/index',['page'=>input('page')]))->ApiSuccess($re);
           	}else{
           		Api()->setApi('msg',$re)->setApi('url',0)->ApiError();
           	}
        }
    	return view(
    		['authGroup'=>$authGroup,'admin_info'=>$admin_info,'page'=>$page,]
    	);
    }

    /**
     * 新增管理员
     */
    public function add ()
    {
    	$authGroup =model('AuthGroup')->where('group_id != 1')->select();
    	resultToArray($authGroup);
    	if (request()->isAjax()) {
    		$data =input();
       	    $re =model('Admin')->add_admin($data);
           	if($re >0){
           		Api()->setApi('url',url('Admins/index'))->ApiSuccess($re);
           	}else{
           		Api()->setApi('msg',$re)->setApi('url',0)->ApiError();
           	}
        }
    	return view('',['authGroup'=>$authGroup,]);
    }

    /**
     * 删除管理员
     */
    public function del ()
    {
      if(request()->isAjax()){
        $time = time();
        $data = input();
        if (in_array(1,(array)$data['id'])) {
            Api()->setApi('url',0)->setApi('msg','超级管理员不能删除')->ApiError();
        }
        $obj =$this->setStatus('admin',$time,$data['id'],'','delete_time');
        if(1 == $obj->code){
            $obj->setApi('url',input('location'))->apiEcho();
        }else{
            $obj->setApi('url',0)->apiEcho();
        }
      }
    }

    /**
     * 改变状态：启用|禁用
     */
    public function change_status ()
    {
        input('admin_id') == 1 && Api()->setApi('url',0)->setApi('msg','不能禁用超级管理员')->ApiError();
        $obj =$this->setStatus('admin',input('status'),input('admin_id'),'admin_id');
    	if(1 == $obj->code){
            session('user .status', input('status'));
    		$obj->setApi('url',input('location'))->apiEcho();
    	}else{
    		$obj->apiEcho();
    	}
    }

    /**
     * 设置用户组
     */
    public function setgroup ()
    {
        $authGroup =model('AuthGroup')->where(['group_id'=>['neq','1']])->select();
        resultToArray($authGroup);
        $admin_id=input('admin_id');
        $admin_info=Admin::get($admin_id)->toArray();
        $admin_group =$admin_info['group'];
        $page =input('page');
        if(request()->isAjax()){
            $data =input();
            $re =model('Admin')->allowField(true)->save($data,['admin_id'=>$data['admin_id']]);
            if($re){
                Api()->setApi('url',url('Admins/index',['page'=>input('page')]))->ApiSuccess();
            }else{
                Api()->ApiError();
            }
        }
        return view('',[
            'authGroup'=>$authGroup,'admin_id'=>$admin_id,'admin_group'=>$admin_group,'page'=>$page,
        ]);
    }

    /*
     * 管理员修改自己的资料
     * */
    public function admin_info ()
    {
        if(request()->isAjax()){
            $data=input();
            if(isset($data['url'])){
                unset($data['url']);
            }
            $re = model('admin')->edit_admin($data);
            if($re >0 ){
                $admin = Session::get('user');//重新给SESSION赋值
                $arr = ['nickname','phone','email'];
                $length = count($arr);
                for ($i=0;$i<$length;$i++){
                    $name = $arr[$i];
                    $admin[$name]=$data[$name];
                    Session::set('user',$admin);
                }
               Api()->setApi('url',url('',['page'=>input('page')]))->ApiSuccess($data['nickname']);
            }else{
                Api()->setApi('msg',$re)->setApi('url',0)->ApiError();
            }
        }else{
            $info = Session::get('user');
            $pash = "./upload/".$info['headimg'];
            if(!file_exists($pash)){//检测图片图片
                $info['headimg']="";
            }
        }
        return view(['info'=>$info]);
    }

  /*
   * 管理员修改自己的密码
   * */
    public function admin_password ()
    {
       if(request()->isAjax()){
           $data=input();
           $data['admin_id'] = session('user.admin_id');
           if($data['new_password'] == $data['confirm_password'] && $data['new_password'] !='' &&  $data['confirm_password'] !="" ) {//两次密码是否一致
               $password= model('admin')->where(['admin_id'=>$data['admin_id']])->field('password')->find()->toArray();//提取原密码
               //原密码解密
               $original_password  =md5($data['original_password']);// Encrypt::authcode( $password['password'],'DECODE');//解密
               $old_password = $password['password'];
               $msg['code'] = 0;
               $msg['msg'] = '';
               if ($old_password == $original_password) {//查看原密码是否输入正确
                   $re = model('admin')->password_edit($data);
                   if ($re) {
                       $msg['code'] = 1;
                       $msg['msg'] = "修改成功";
                   } else {
                       $msg['msg'] = "修改失败";
                   }
               } else {
                   $msg['msg'] = "原密码有误";
               }
           }else{
               $msg['msg'] = "两次密码不一致";
           }
           return $msg;
       }else{
           $admin_id = Session::get('islogin');
       }
        return view(['admin_id'=>$admin_id]);
    }

    /*
     * 修改头像
     * */
    public function admin_headimg ()
    {
        if(request()->isAjax()){
            $admin = Session::get('user');
            $img  = $admin['headimg'];//原图片
            $info = upload_headimg('avatar_file','admin');
            if($info){
                if(!empty($img)){//数据表有图片
                    $pash = "./upload/".$img;
                    if(file_exists($pash)){//删除原图片
                        unlink($pash);
                    }
                }
                $data['admin_id'] = Session::get('islogin');
                $data['headimg']  =$info;
                $re = model('admin')->edit_headimg($data);
                if ($re) {
                   $admin['headimg'] = $data['headimg'];
                   Session::set('user',$admin);
                    Api()->setApi('url', url('Admins/admin_info', ['page' => input('page')]))->ApiSuccess(config('static_url').'/upload/'.$info);
                } else {
                    Api()->ApiError();
                }
            }else{
                Api()->setApi('msg', '上传失败,请查看格式和大小!')->setApi('url', 0)->ApiError();
            }
        }
    }
}
