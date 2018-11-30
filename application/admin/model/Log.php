<?php
namespace app\admin\model;
use think\Model;
use traits\model\SoftDelete;
/**
 * 操作日志模型
 * @author pengqiang
 * @version  2017/5/15
 */
class Log extends Model{
    use SoftDelete;
    protected $deleteTime          = 'delete_time';
    protected $readonly            = [];//只读字段

    /**
     * 清空数据
     */
    public function delAll()
    {
    	// return $this->execute($sql = 'TRUNCATE table `ui_action`');
        return $this->where("1=1")->delete();
    }

    /**
     * 操作日志查询
     */
    public function select_log($data,$where=array())
    {
    	extract($data);
    	if(isValue($data,'statr_time') && isValue($data,'end_time')){
            $statr_time                = strtotime($statr_time);
            $end_time                  = strtotime($end_time);
			$where['create_time']    = ['BETWEEN',[$statr_time,$end_time]];
		}
        $query                         = $data;
		$list                          = $this->where($where)->order('create_time asc')->paginate('',false,['query' => $query]);
		resultToArray($list);
		return $list;
    }

    /**
     * 操作记录
     * $data  string  操作对象
     * 
     */
    public function add_log($data)
    {
        if(strtolower(ACTION_NAME) == 'login'){
                $des                   = '登录系统';
                $admin_info            = model('admin')->where('account',$data)->find()->toArray();
                $admin_id              = $admin_info['admin_id'];
                $log                   = [
                    'admin_id'       => $admin_id,
                    'module'          => MODULE_NAME,
                    'controller'     => CONTROLLER_NAME,
                    'action'          => ACTION_NAME,
                    'data_id'         => $data,
                    'des'              => $des,
                ];
        }else{
            $menu_c                     = model('menu')->where('controller',strtolower(CONTROLLER_NAME))->find()->toArray();
            $menu_a                     = model('menu')->where('action',strtolower(ACTION_NAME))->find()->toArray();
            $userinfo                   = session('user');
            $account                    = $userinfo['account'];
            $des                        = $menu_a['menu_name'].$menu_c['menu_name'].$data;
            $log                        = [
                 'admin_id'           => $userinfo['admin_id'],'module'=>MODULE_NAME,
                 'controller'         => CONTROLLER_NAME,'action'=>ACTION_NAME,'data_id'=>$data,
                 'des'                 => $des,
            ];
            
        }
        return $this->save($log);
        
    }
}
