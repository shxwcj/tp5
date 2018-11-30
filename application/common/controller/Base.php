<?php
namespace app\common\controller;
header('content-type:text/html;charset=utf-8');
use think\Controller;
use think\Request;
use think\Session;
use extend\Encrypt;
/**
 * 基础类
 * by chick 2017-05-02
 */
class Base extends Controller
{
	protected $base_url,$var;
    public function __construct(){
        parent::__construct();
    }
    public function _initialize(){
        $this->base_setConfig(config('app_debug'));
        $this->base_setUrl();
        $this->base_setVar();
        Session::init(['prefix' => MODULE_NAME,]);
        $this->qiangOut();
    }

    /**
     * 设置常用url为常量
     *
     */
    final protected function base_setUrl(){
        //$root="/alasika";
        $root="";
    	$this->base_url['module_name']     = Request::instance()->module();//当前模块
    	$this->base_url['controller_name'] = Request::instance()->controller();//当前控制器
    	$this->base_url['action_name']     = Request::instance()->action();//当前方法
    	$this->base_url['public_path']     = $root.'/public/';
    	$this->base_url['static_path']     = $root.'/public/static/';
    	foreach ($this->base_url as $name => $value) {
    		$name = strtoupper($name);
    		!defined($name) && define($name, $value);
    	}
        $this->base_url['js']  =$root.'/public/theme/'.MODULE_NAME.'/static/js/';
        $this->base_url['css'] = $root.'/public/theme/'.MODULE_NAME.'/static/css/';
        $this->base_url['img'] = $root.'/public/theme/'.MODULE_NAME.'/static/img/';
        $this->base_url['imgs'] = $root.'/public/theme/'.MODULE_NAME.'/static/imgs/';
        $this->assign($this->base_url);//加载到模版
    }

    final protected function base_setVar(){
        $web                       = model('web')->getWeb();//获取网站基本信息
    	$this->var['title']       = config('web_name');
    	//$this->var['admin_title'] = config('web_admin_name')?:config('web_name').'猎隼管理后台模版';
        $this->var['admin_title'] = config('web_admin_name')?:$web['name'];
        $this->var['logo']        = 'upload/'.$web['logo'];
        $this->var['nowpage']     = input(config('paginate.var_page'))?:1;
        $this->var['upload']      = ROOT_PATH.'public/upload/';
    	$this->assign($this->var);//加载到模版
    }
    /**
     * 登录判断
     */
    final protected function is_login(){
        $api    = Api('this');
        $user_id = session('islogin');
        if (!$user_id) {
            return $api->setApi('msg','未登录')->ApiError();
        } else {
            return $api->setApi('msg','-')->ApiSuccess();
        }
    }

    /**
     * 判断URL链接前是否添加Http,/www.
     */
    public function getUrl($url){
        $p1 = '/^(.*?)www\.(.*)$/';
        $p2 = '/^http(.*)$/';
        $re1 = preg_match($p1,$url);
        if (!$re1){
            $url = $url;
        } else {
            $re2 = preg_match($p2,$url);
            if (!$re2){
                $url = 'http://'.$url;
            }
        }
        return  $url;
    }

    /**
     * 初始化系统配置文件
     */
    final protected function base_setConfig($force = false){
        if ($force && Request::instance()->isAjax()) { return false; }
        $file = CONFIG_PATH.'/config.cache.php';
        !is_dir(dirname($file)) && mkdir(dirname($file),'0777',true);
        !file_exists($file)     && $force = true;
        //判断是否强制重写
        if (!$force) {//非强制重写
            $time = getFileTime($file);
            $compare =  time()-$time;
            if ($compare <= config('cache.expire')) return false;//文件更新时间小于设置时间
        }
        $model = model('config');
        $configs = $model->select();
        $config  = "<?php \n//此配置文件由系统生成，修改无效\nreturn [";
        foreach ($configs as $v) {
            $v['config_des']  = $v['config_des'] ?: $v['config_name'];
            $v['config_mark'] = strtolower($v['config_mark']);
            $config .= "\n\t'{$v['config_mark']}'=>'{$v['config_value']}',//{$v['config_des']}\n";
        }
        $config .= "\n];";
        if (!$force && md5(file_get_contents($file)) == md5($config)) return false;//文件内容相同
        $f = fopen($file,'w+');
        fwrite($f,$config);
        fclose($f);
        return true;
    }


   /**
     * 检测用户是否登录
     * @return true 已登录  false 未登录
     *
     */
   /* final protected function is_login(){
        $api    = Api('this');
        $user_id = session('islogin');
        if (!$user_id) {
            return $api->setApi('msg','未登录')->ApiError();
        } else {
            $login_time = session('user.last_login_time');
            $map['admin_id'] = session('user.admin_id');
            $admin = model('admin')->get($map)->toArray();
            if($login_time != $admin['last_login_time']){
                return $api->setApi('msg','帐号在其它浏览器登录！')->ApiError();//1 账号被占用
            }
            if(config('login_timeout') != 0){
                $logintimed =time()-$login_time;
               if( $logintimed > config('login_timeout')){
                    return $api->setApi('msg','登录超时')->ApiError();//2 登录超时
                }
            }
            return $api->setApi('msg','-')->ApiSuccess();
        }
    }*/

    /**
     * 清除登录信息
     */
    final protected function destroyUser(){
        session('islogin',0);
        session('user',[]);
    }

    /**
     * 被禁用就退出
     */
    public  function  qiangOut(){
        $user  =  session('user');
        if(isset($user['admin_id'])){
            $bool   = model('Admin')->where(['admin_id'=>$user['admin_id']])->field('status')->find();
            if(isset($bool['status']) && $bool['status'] == 0){
                session_destroy();
                die(Api()->setApi('url',url('admin/publics/login')));
            }
        }
    }
}
