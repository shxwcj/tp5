<?php
namespace app\admin\controller;

use app\admin\controller\AdminBase;
use app\admin\model\Users;
use think\Session;
use think\Config;
/**
* 账户管理
* by iwater 20170821
 * @author pengqing
*/
class User extends AdminBase
{
	private $status                    = ['','禁用', '启用'];

	public function index(){
		//设置分页显示参数:list_row
		setPageParam(3);
		$data                            = input();
		$lists                           = model('User')->select_user($data);
		// dump($lists);
		foreach ($lists as $key => &$value) {
			$value['statusname']       = $this->status[$value['status']];
			$value['group_id']         = model('auth_group')->where("group_id = {$value['group_id']}")->value('group_name');
			$value['agent_id']         = model('agents')->where("agent_id = {$value['agent_id']}")->value('agent_name');
		}
		return view([
			'lists'		            => $lists,
		]);
	}

	public function add_user(){
		if(request()->isPost()){
			$data                      = input();
			// dump($data);die;
			if($data['repassword'] != $data['password']){
				Api()->setApi('msg','确认密码错误')->setApi('url',0)->ApiError();
			}else{
				unset($data['repassword']);
			}
			$data['password']        = md5($data['password']);//md5加密
			$rs                        = model('User')->add_user($data);
			if($rs>0){
				Api()->setApi('url', url('User/index'))->ApiSuccess($rs);
			}else{
				Api()->setApi('msg', $rs)->setApi('url',0)->ApiError();
			}
		}
		$groups                        = db('auth_group')->field('group_id, group_name')->select();
		$agents                        = db('agents')->field('agent_id, agent_name')->select();
		return view([
			'groups' 	               => $groups,
			'agents'	               => $agents
		]);
	}

	public function edit_user(){
		if(request()->isPost()){
			$data                     = input();
            // dump($data);die;
			$rs                       = model('User')->edit_user($data);
			if($rs>0){
				Api()->setApi('url', url('User/index'))->ApiSuccess($rs);
			}else{
				Api()->setApi('msg', $rs)->setApi('url',0)->ApiError();
			}
		}else{
			$where['id']             = input('id');
			$lists                    = db('User')->where($where)->find();
			// dump($lists);die;
			$groups                   = db('auth_group')->field('group_id,group_name')->select();
			$agents                   = db('agents')->field('agent_id,agent_name')->select();
			return view([
				'lists'		      => $lists,
				'groups' 	          => $groups,
				'agents'	          => $agents
			]);
		}
	}

	public function del_user(){
		if(request()->isAjax()){
            $time                    = time();
            $data                    = input();
            $obj                     = $this->setStatus('User',$time,$data['id'],'id','delete_time');
            if(1 == $obj->code){
                Api()->setApi('url', input('location'))->ApiSuccess();
            }else{
                Api()->setApi('url', 0)->ApiError();
            }
        }
	}

	public function user_status(){
		$status                     = input('status');
        $id                         = input('id');
        $url                        = input('location',url('User/index'));
        $re                         = $this->setStatus('User',$status,$id,'id');
        if($re->code == 1){
            Api()->setApi('url', $url)->ApiSuccess($re);
        }else{
            Api()->setApi('url', 0)->ApiError();
        }
	}

	public function export(){
		$title                     = '账户管理表';
        $data                      = model('User')->select();
        // dump($data);die;
        $newDate                   = [];
        foreach ($data as $key => $value) {
        	$status                = $this->status[$value['status']];
			$group_name            = model('auth_group')->where("group_id = {$value['group_id']}")->value('group_name');
			$agent_name            = model('agents')->where("agent_id = {$value['agent_id']}")->value('agent_name');
        	$newDate[]             = [
        		'操作'			   => '',
        		'员工姓名'		   => $value['username'],
        		'所属角色'		   => $group_name,
        		'账户名称'		   => $value['nickname'],
        		'所属机构'		   => $agent_name,
        		'区域编码'		   => $value['area_code'],
        		'状态'			   => $status,
        		'描述'			   => $value['des'],
        		'创建时间'		   => $value['create_time'],
        		'更新时间'		   => $value['update_time']
        	];
        }
        $fieldName                 = array_keys($newDate[0]);
        $data                      = $newDate;
        // dump($fieldName);die;
        /**
		 * 导出数据
		 * @param  data 	 array 		需要导出的数据二位数组
		 * @param  fieldName array  	需要表格的标题栏
		 * @param  title 	 string 	需要导出的数据表名称
		 */
        $this->push($data,$fieldName,$title);
	}

	//修改密码
	public function set_pwd(){
		if(request()->isAjax()){
			$data                 = input();
			// dump($data);die;
			$map['id']           = $data['id'];
			$old_password         = model('User')->where($map)->value('password');
			if(md5($data['old_password']) != $old_password){
				Api()->setApi('msg', '请输入正确的旧密码')->setApi('url',0)->ApiError();
			}else{
				unset($data['old_password']);
			}
			if($data['password'] != $data['repassword']){
				Api()->setApi('msg', '确认密码不正确')->setApi('url',0)->ApiError();
			}else{
				unset($data['repassword']);
			}
			
			$data['password']    = md5($data['password']);//md5加密
			$rs                    = model('User')->edit_user($data);
			if($rs>0){
				Api()->setApi('url', url('User/index'))->ApiSuccess($rs);
			}else{
				Api()->setApi('msg', $rs)->setApi('url',0)->ApiError();
			}
		}
		return view();
	}
}