<?php
namespace app\common\model;
use think\Model;
use traits\model\SoftDelete;

/*
 * @table   User
 * @Auth weichunfeng
 * @version 2017.08.21
 * */

class User extends Model{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $readonly = [];//只读字段
//    protected $auto = ['status'=>1];//自动完成

    public function select_user($data,$where=array()){
    	if(!empty($data)){
    		$startTime = empty($data['start_time']) ? 0 : strtotime($data['start_time']);
       		$endTime   = empty($data['end_time']) ? 0 : strtotime($data['end_time']);
	        if (!empty($startTime) && !empty($endTime)) {
	            $where['create_time'] = [['>= time', $startTime], ['<= time', $endTime]];
	        } else {
	            if (!empty($startTime)) {
	                $where['create_time'] = ['>= time', $startTime];
	            }
	            if (!empty($endTime)) {
	                $where['create_time'] = ['<= time', $endTime];
	            }
	        }

	        if (!empty($data['username'])) {
	            $where['username'] = ['like','%'.(string)$data['username'].'%'];
	        }
	       
	        if (!empty($data['status'])) {
	            $where['status'] = $data['status'];
	        }
	        // dump($data);dump($where);die;
    	}
    	
        if(isset($data['page'])){
            $page = $data['page'];
            unset($data['page']);
        }else{
            $page =1;
        }

        $list = $this->where($where)->order('create_time desc')->paginate('',false,['page'=>$page,'query' => $data]);
        resultToArray($list);
        return $list;
    }

    public function add_user($data){
    	 $result = $this->validate('User.add')->save($data);
        if($result === false){
            return $this->getError();
        }else{
            return $result;
        }
    }

    public function edit_user($data){
    	$result = $this->validate('User.edit')->save($data,['id'=>$data['id']]);
        if($result === false){
            return $this->getError();
        }else{
            return $result;
        }
    }
}