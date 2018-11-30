<?php
namespace app\admin\controller;
use extend\Encrypt;
/**
 * 后台公共控制器
 * Created by PhpStorm.
 * @author pengqing
 * User: THINK
 * Date: 2018/5/08
 * Time: 15:20
 */

class Publics extends AdminBase{

	public function __construct(){
        parent::__construct();
        $this->view->engine->layout(false);
    }
    /**
     * 后台登录
     */
	public function login(){

		if(request()->isAjax()){
			$account                    = trim(input('username'),' ');
			$password                   = trim(input('password'),' ');
			if(!$account){
                Api()->setApi('msg', "请输入用户名")->setApi('url', 0)->ApiError();
            }elseif (!$password){
                Api()->setApi('msg', "请输入密码")->setApi('url', 0)->ApiError();
            }
            $user_info                 = $this->getInfoByAccount($account);
            $this->checkUser($user_info);
            $authcode                  =$user_info['password'] ;// Encrypt::authcode($user_info['password'],'DECODE');
            $password                  = md5($password);
            if($password == $authcode){
        		session('islogin', $user_info['admin_id']);
                unset($user_info['password']);
                $user_info['last_login_time'] = time();
                session('user',$user_info);
                $bool['status'] =1;
                model('Admin')->where(['admin_id'=>$user_info['admin_id']])->update(['last_login_time'=> $user_info['last_login_time']]);
//                Api()->setApi('msg', "登录成功！")->setApi('url', url('Index/index'))->ApiSuccess();
        	}else{
                $bool['status'] =0;
//        		 Api()->setApi('msg', "密码错误")->setApi('url', 0)->ApiError();
        	}
        	return  json($bool);
		}else{
            return view();
        }

	}

	/**
	 * 获取用户信息
	 */
	public function getInfoByAccount($account){
		$info                        = model('admin')->where('account',$account)->find();
		if(is_object($info)){
			$info                    = $info->toArray();
			return $info;
		}else{
			Api()->setApi('msg', "用户不存在")->setApi('url',0)->ApiError();
		}
	}

	/**
	 * 检验用户及用户组状态 
	 */
	public function checkUser($user_info){
		if($user_info['status'] == 0){
			Api()->setApi('msg', "用户不存在")->setApi('url', 0)->ApiError();
		}
		$group_status               = model('AuthGroup')->where('group_id', $user_info['group'])->value('status');
		if($group_status == 0){
			Api()->setApi('msg', "用户所在用户组被禁用")->setApi('url', 0)->ApiError();
		}
		return true;
	}
	/**
	 * 登录成功更新数据
	 */
    public function updateDate($user_info){
        //更新用户登录信息
        $loginStatus = $this->isMobile();//判断是否是 mobile 登录 or pc 登录
        $data = array(
            'admin_id'              => $user_info['admin_id'],
            'last_login_time'      => time(),
            'last_login_ip'        => get_client_ip(),
            'login_status'         => $loginStatus
        );
        model('admin')->save($data,['admin_id',$user_info['admin_id']]);
    }
    /**
     * 判断登陆设备
     */
    public function isMobile(){
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])){
            return 2;
        }else{
            return 1;
        }
    }

    /**
     * 退出登录
     */
    public function loginOut(){
    	$this->destroyUser();
    	return true;
    }
    /**
     * 退出前修改登录状态
     */
    public function changeLoginStatus(){

    }

    /*
     * 上传附件
     * */
    public function upload_file($fileName='', $type='image', $path=''){
        // 获取表单上传文件 例如上传了001.jpg
        return upload_file($fileName, $type, $path);
    }

    public function delete_file($path, $name = ''){
        $res                   = delete_file($path, $name);
        return $res;
    }
    /*public function delete_file($path='',$name=''){
        $res=delete_file($path,$name);
        return $res;
    }*/

    /*
     * 删除指定表记录
     * $soft_delete是否软删除
     */
    public function del_record($table,$pk_value,$soft_delete=true,$pk){
        if(request()->isAjax()){
            if($soft_delete){
                $time        = time();
                $obj         = $this->setStatus($table,$time,$pk_value,$pk,'delete_time');
            }else{
                $obj         = model($table)->where($pk,$pk_value)->delete();
            }
            if(1 == $obj->code){
                Api()->setApi('url', '')->ApiSuccess();
            }else{
                Api()->setApi('url', 0)->ApiError();
            }
        }
    }

}