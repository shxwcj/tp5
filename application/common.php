<?php
ob_end_clean();
header("Content-type:text/html;charset=utf-8");
use think\Image;
use think\File;
use think\Loader;
use think\Request;
use think\Config;
use think\Session;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function resultToArray(&$results){
    foreach ($results as &$result) {
        $result        = $result->getData();
    }
}

function getTree($data,$options=[], $level=0){
    return new \extend\Tree($data, $options, $level);
}
function Api($type = '', $setApi = false){
    $app_debug        = config('app_debug');
    $api              = new \app\common\controller\Api($app_debug);
    return $api->setType($type, $setApi);
}
/**
 * 判断值是否为空
 */
function isValue($data,$key=false){
    if ($key !== false) {
        if(!is_array($data)) return false;
        if(!array_key_exists($key, $data)) return false;
        $v            = $data[$key];
    } else {
        $v            = $data;
    }
    if ($v === 0 || $v === '0') return true;
    if($v != '') return true;
    if (is_array($v) && $v != []) return true;
    return false;
}
function getNamebyPk($model,$pk_name,$getField,$pk_value){
    return  model($model)->where([$pk_name => $pk_value])->value($getField);
}
/**
 * 获取图片用于显示
 */
function getImg($imgName,$isUrl=false){
    if ($isUrl) {
        $url          = $imgName;
    } else {
        $url          = config('STATIC_URL').'/upload/'.$imgName;
        $url_t        = ROOT_PATH.'public/upload/'.$imgName;
    }
    if (!is_file($url_t)) {
        $url          = config('static_url').'/upload/'.config('default_img');
        $url_t        = ROOT_PATH.'public/upload/'.config('default_img');
        $url          = is_file($url_t) ? $url : config('static_url').'/static/img/default1.png';
    }
    return $url;
}

/**
 * 获取附件
 */
function getFile($fileName,$isUrl=false){
    if ($isUrl) {
        $url          = $fileName;
    } else {
        $url          = config('STATIC_URL').'/public/upload/enclosure/'.$fileName;
        $url_t        = ROOT_PATH.'public/upload/enclosure/'.$fileName;
    }
    if (!is_file($url_t)) {
        $url          = config('static_url').'/upload/'.config('default_img');
        $url_t        = ROOT_PATH.'public/upload/'.config('default_img');
        $url          = is_file($url_t) ? $url : config('static_url').'/static/img/default1.png';
    }
    return $url;
}

function get_login_user_name(){
    return session('user.nickname') ?:session('user.account');
}
function get_login_admin_group(){
    $group          = session('user.group');
    if (!$group) { return;}
    $name           = model('auth_group')->where(['group_id' => $group])->value('group_name');
    return $name;
}

function buildRandomString($type = 1, $length = 4){
    if($type == 1){
        $chars      = join("",range(0,9));
    }elseif($type == 2){
        $chars      = join("",array_merge(range("a","z"),range("A","Z")));
    }elseif($type == 3){
        $chars      = join("",array_merge(range("a","z"),range("A","Z"),range(0,9)));
    }
    $chars          = str_shuffle($chars);
    return substr($chars,0, $length);
}

/* $name  string    为type="file"的input框的name值
 * $file string     存在图片的文件夹 (文件夹必须在upload之下)
 * return  string   返回图片的文件夹和名字
 * */
function upload_headimg($name, $file){
    $up_dir        = "./upload/$file";
    if (!file_exists($up_dir)) {
        mkdir($up_dir, 0777, true);
    }
    $image         = Image::open(request()->file($name));//打开上传图片
    $size          = input('avatar_data');//裁剪后的尺寸和坐标
    $size_arr      = json_decode($size,true);
    $type          = substr($_FILES [$name]['name'],strrpos($_FILES [$name]['name'],'.')+1);
    $name          = time().".".$type;
    $info          = $image->crop($size_arr['width'], $size_arr['height'],$size_arr['x'],$size_arr['y'])->save("./upload/$file/$name");
    if($info){
        return $file."/".$name;
    }else{
        return false;
    }
}
//上传文件
function upload_file($fileName = '', $type = 'image', $path = ''){
    if($type =='file'){
        $path            = $path ? $path : config("ENC_PATH");
    }else{
        $path            = $path ? $path : config("IMG_PATH");
        $config['exts'] = 'jpg,gif,png,jpeg';
    }
    $config              = config("upload");
    $config['rootPath'] = config("UPLOAD_ROOT_PATH");
    $config['savePath'] = $path;
    $uploader            = new \org\Upload($config);
    $info = $uploader->upload();
    if(!$info){
        echo $uploader->getError();
    }else{
        $data           = current($info);
        $data['file_path'] = $config['rootPath'].$data['savepath'].$data['savename'];
        return $data;
    }
}
/**
 *上传base64编码
 * @par  $base64   base64编码
 * @par  $path  文件夹名字 相对于/upload; 比如"Headimg"
 * @by pengqiang
 */
function uploadBase64($base64,$path){
    $year = date('Y/m',time());//图片路径 (年/月)  自己设置
    $up_dir = "./upload/".$path."/$year/";//存放在当前目录的upload文件夹下
    if(!file_exists($up_dir)){
        mkdir($up_dir,0777,true);
    }
    if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result)){
        $type = $result[2];//图片格式
        if(in_array($type,array('pjpeg','jpeg','jpg','gif','bmp','png'))){
            $new_file = $up_dir.date(time()).rand(1,1000).'.'.$type;  //time()时间戳作为图片名字
            if(file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64)))){
                $img_path = str_replace("$up_dir", '', $new_file);//提取图片名字 和 格式
                $image_url = $path."/$year/$img_path";//用作保存的图片路径和名字
                return  $image_url;
            }
        }
    }
    return false;
}
/**
 * 判断头像是否为空 或者 头像是否存在
 * $img 图片路径,相对于/upload;如(/image/2017/11/image01510285141.jpeg)
 * by pengqiang
 */
function head_img($img){
    if(!$img || !file_exists('./upload/'.$img)) return '/public/static/img/default2.png';//没有图片(图片不存在)显示默认头像
    return '/upload/'.$img;
}
/**
 * app专用验证
 * 判断头像是否为空 或者 头像是否存在
 * $img 图片路径,相对于/upload;如(/image/2017/11/image01510285141.jpeg)
 * by pengqiang
 */
function check_image($img){
    if(!$img || !file_exists('./upload/'.$img)) return false;
    return true;
}
/**
 * 删除图片(单张)
 * @param string  $img
 */
function delete_one_img($img){
    if(file_exists("./upload/".$img)){
        unlink("./upload/".$img);
    }
}

/**
 * 删除图片(批量)
 * @param string  $img
 */
function deleteImgAll($data){
    foreach ($data as $k=>$v){
        if(file_exists("./upload/".$v)){
            unlink("./upload/".$v);
        }
    }
}

function delete_file($path = '', $name = '', $type = 'image'){
    if(empty($path)){
        if($type=='file'){
            $path=config("ENC_PATH").$name;
        }else{
            $path=config("IMG_PATH").$name;
        }
    }
    if (!file_exists($path)) {
        return "文件不存在";
        //return true;
    }else{
        return unlink($path);
    }
}

//状态文字描述
function text_status($status)
{
    $state                = '';
    $status               == 1 ? $state = '启用' : $state = '禁用';
    return $state;
}

//通过交易号查order_id
function getOrderIdBy_transactionId($tansactionId){

}
//通过咖啡机编号查询device_id
function getOrderIdBy_deviceCode($device_code){
    
}


/**
 * thikphp5 已删除C/D/U/M/W/I等函数
 * 重写单字母函数C/D/U/M/W/I
 * by chick 2017-05-02
 */
function C($name = '', $value = null, $range = ''){
    return config($name, $value, $range );
}
function D($name = '', $layer = 'model', $appendSuffix = false){
    return model($name, $layer, $appendSuffix);
}
function M($name = '', $config = [], $force = true){
    return db($name, $config, $force);
}
function U($url = '', $vars = '', $suffix = true, $domain = false){
    return url($url, $vars, $suffix, $domain);
}
function W($name, $data = []){
    return widget($name, $data);
}
function I($key = '', $default = null, $filter = ''){
    return input($key, $default, $filter);
}

/*通过log_id查询操作人账户*/
function getAccount($log_id){
    $admin_id              = model('log')->where("log_id = {$log_id}")->value('admin_id');
    $account               =  model('admin')->where("admin_id = {$admin_id}")->value('account');
    return $account;
}


//随机字符串和数字组合
function getRandom($param){
    $str                   = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $key                   = "";
    for($i=0;$i<$param;$i++)
    {
        $key              .= $str[mt_rand(0,61)];    //生成php随机数和字母的组合
    }
    return  $key;
}

//随机数字
function getRandCode($param){
    $str                   = "0123456789";
    $key                   = "";
    for($i = 0; $i < $param; $i++)
    {
        $key              .= $str[mt_rand(0, 9)];    //生成php随机数和字母的组合
    }
    return $key;
}


//设置分页参数

function setPageParam($list_rows){
    config(['paginate' => ['type' => 'bootstrap', 'list_rows' => $list_rows, 'var_page'  => 'page']]);
    Session::set('pageSize', config('paginate.list_rows'));
}

//将路径转为文件名为键值的路径
function fileNamePath($path){
    if($path){
        $key=basename($path);
        $data[$key]          = $path;
        return $data;
    }
}

//获取数据字典中字段html
function diction_option($encoding,$selected='',$type="select"){
    $data                       = model("dictionarys")->where("di_encoding ='{$encoding}'")->field("di_name,option_value")->find()->toArray();
    if($data){
        $option_value           = unserialize($data['option_value']);
        $html                   = '';
        if($type=="select"){
            $html               = "<option value = '' selected = 'selected'>请选择".$data['di_name']."</option>";
        }
        foreach ($option_value as $k => $v){
            if(!empty($selected)){
                if($k == $selected){
                    if($type == "select"){
                        $html  .="<option value='".$k."' selected='selected'>".$v."</option>";
                    }elseif($type=="radio"){
                        $html  .="<label class=\"i-lab\"><input type=\"radio\" name=\"".$encoding."\" value=\"".$k."\" class=\"mgr mgr-primary\" checked><span>".$v."</span></label>";
                    }
                    continue;
                }
            }
            if($type == "select") {
                $html          .= "<option value='" . $k . "'>" . $v . "</option>";
            }elseif ($type == "radio"){
                $html          .= "<label class=\"i-lab\"><input type=\"radio\" name=\"".$encoding."\" value=\"".$k."\" class=\"mgr mgr-primary\"><span>".$v."</span></label>";
            }
        }
        return $html;
    }else {
        return false;
    }
}

function get_diction($encoding){
    $data                        = model("dictionarys")->where("di_encoding = '{$encoding}'")->field("di_name, option_value")->find()->toArray();
    if($data){
        $option_value            = unserialize($data['option_value']);
        return $option_value;
    }else{
        return false;
    }
}

/**
 * 阿里大鱼短信接口
 */
function send_cms($data){
    $code = rand(100000,999999);
    $info  =[
        'tel'=>$data['account'],//电话
        'code'=>$code,//验证码
        'name'=>'烯旺科技',//签名
        'ban'=>'SMS_83175087',//模板
    ];
   //调用发送短信
    vendor('sms.api_demo.SmsDemo');
    $shi = new \SmsDemo();
    $send = $shi ->sendSms($info);
    if(is_object($send)) {
        $array = (array)$send;
    }elseif(is_array($send)) {
        foreach($send as $key=>$value) {
            $array[$key] = object_array($send);
        }
    }
    if($array['Code'] == 'OK'){
        $sms['account'] = $data['account'];
        $sms['code'] = $info['code'];
        model('sms')->addCode($sms);
        return true;
    }else{//返回内容异常，以下可根据业务逻辑自行修改
        return false;
    }
}

/**中国移动数据接口
 * 发送短信
 * @param  [string] $tel     手机号
 * @param  [type] $content   短信内容
 * @return [type]            发送状态
 */
function sendMessage($tel,$content){
    $url 		                 = "http://www.ztsms.cn/sendSms.do";//提交地址
    $username 	                 = 'liesunsmszh';//用户名
    $password 	                 =  'Liesun666';//原密码
    vendor('sendAPI.sendAPI');
    $sendAPI                     = new \sendAPI($url, $username, $password);
    $data                        = array(
        'content' 	            => $content.'【掌赢通】',//短信内容
        'mobile' 	            => $tel,//手机号码
        'productid'            => '676767',//产品id
        'xh'		            => ''//小号
    );
    $sendAPI->data = $data;//初始化数据包
    $return                     = $sendAPI->sendSMS('POST');//GET or POST

    return $return;
}



//性别文字描述
function text_sex($status)
{
    $state = '';
    switch ($status){
        case 1: $state = '男';break;
        case 2: $state = '女';break;
        default: $state = '未知';break;
    }
    return $state;
}


//获取文件后缀
function get_extension($file)
{
    return substr(strrchr($file, '.'), 1);
}

function logger($content){
    $logSize               = 100000;
    $log="wechat_log.txt";
    if(file_exists($log) && filesize($log)  > $logSize){
        unlink($log);
    }
    file_put_contents($log,date('H:i:s')." ".$content."\n",FILE_APPEND);
}

/**聚合数据短信接口
 * 发送手机短信
 * @param $data
 */
function juhe_cms($data)
{
    $code = rand(100000,999999);
    $senUrl = 'http://v.juhe.cn/sms/send';//短信接口的URL;
    $smsConf  =array(
        'key' => '9c1f1c3a882f64374b320d005349d402',//您申请的APPKEY
        'mobile' => $data['account'],//接受短信的用户手机号码
        'tpl_id' => '68073',//您申请的短信模板ID，根据实际情况修改
        'tpl_value' => '#code#='.$code,//您设置的模板变量，根据实际情况修改
    );
    $status = juhecurl($senUrl,$smsConf,1);//请求发送短信 
    if($status){
        $smsArr = json_decode($status,true);
        $errCode = $smsArr['error_code'];
        if($errCode  == 0){//发送成功
            $info['account'] = $data['account'];
            model('sms')->addCode($info);
            return true;
        }else{//发送失败
            $msg  = $smsArr['reason'];
            return $msg;
        }
    }else{//返回内容异常，以下可根据业务逻辑自行修改
        return false;
    }
}
/**
 * 请求接口返回内容
 * @param  string $url [请求的URL地址]
 * @param  string $params [请求的参数]
 * @param  int $ipost [是否采用POST形式]
 * @return  string
 */
function juhecurl($url,$params=false,$ispost=0){
    $httpInfo = array();
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
    curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
    curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
    if( $ispost )
    {
        curl_setopt( $ch , CURLOPT_POST , true );
        curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
        curl_setopt( $ch , CURLOPT_URL , $url );
    }
    else
    {
        if($params){
            curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
        }else{
            curl_setopt( $ch , CURLOPT_URL , $url);
        }
    }
    $response = curl_exec( $ch );
    if ($response === FALSE) {
        //echo "cURL Error: " . curl_error($ch);
        return false;
    }
    $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
    $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
    curl_close( $ch );
    return $response;
}
/**
 * 验证手机
 * $data 手机号码
 */
 function check_mobile($data){
    if(preg_match("/^1[3|4|5|7|8][0-9]{9}$/",$data)){ #验证手机格式
        return true;
    }
    return false;
}
/**
 * 验证验证码
 */
 function check_code($data){
    $sms  = model('sms')->getCode($data);//验证验证码
    if($sms >0){
        return true;
    }else{
        return false;
    }
}

/**
 * 获取时间段
 * @return int
 */
function get_houer(){
    $houer =  date("h");//时
    $wu =  date("a");//am上午pm下午
    $dian = 0;
    if($wu === 'am'){
        switch ($houer){
            case 0;
                $dian = 0;
                break;
            case 1;
                $dian = 0;
                break;
            case 2;
                $dian = 0;
                break;
            case 3;
                $dian = 0;
                break;
            case 4;
                $dian = 1;
                break;
            case 5;
                $dian = 1;
                break;
            case 6;
                $dian = 1;
                break;
            case 7;
                $dian = 1;
                break;
            case 8;
                $dian = 2;
                break;
            case 9;
                $dian = 2;
                break;
            case 10;
                $dian = 2;
                break;
            case 11;
                $dian = 2;
                break;
            default:
                $dian = 0;
                break;
        }
    }elseif($wu === 'pm'){
        switch ($houer){
            case 12;
                $dian = 3;
                break;
            case 1;
                $dian = 3;
                break;
            case 2;
                $dian = 3;
                break;
            case 3;
                $dian = 3;
                break;
            case 4;
                $dian = 4;
                break;
            case 5;
                $dian = 4;
                break;
            case 6;
                $dian = 4;
                break;
            case 7;
                $dian = 4;
                break;
            case 8;
                $dian = 5;
                break;
            case 9;
                $dian = 5;
                break;
            case 10;
                $dian = 5;
                break;
            case 11;
                $dian = 5;
                break;
            default:
                $dian = 0;
                break;
        }
    }
    return $dian;
}
//下面是生成token方法代码
function settoken()
{
    $str = md5(uniqid(md5(microtime(true)),true));  //生成一个不会重复的字符串
    $str = sha1($str);  //加密
    return $str;
}

/**
 * 验证token
 * @param $data
 */
function check_token($data){
    if(!isset($data['token']))  return false;
    if(!isset($data['account']) && !isset($data['member_id']))  return false;
    if(isset($data['account'])){
        $where['account'] =$data['account'];
    }elseif(isset($data['member_id'])){
        $where['member_id'] =$data['member_id'];
    }
    $where['token'] = $data['token'];
    $bool = model('members')->where($where)->field('member_id')->find();
    if(isset($bool['member_id'])){
        return true;
    }else{
        return false;
    }
}

/**
 * APP专用上传图片
 * @param $base64  编码
 * @param $path    路径
 * @param $type    文件格式
 * @return string
 */
function app_upload_base64($base64,$path,$type){
    $year = date('Y/m',time());//图片路径 (年/月)  自己设置
    $up_dir = "./upload/".$path."/$year/";//存放在当前目录的upload文件夹下
    if(!file_exists($up_dir)){
        mkdir($up_dir,0777,true);
    }
    if(in_array($type,array('pjpeg','jpeg','jpg','gif','bmp','png'))){
        $new_file = $up_dir.date(time()).rand(1,1000).'.'.$type;  //time()时间戳作为图片名字
        if(file_put_contents($new_file, base64_decode($base64))){
            $img_path = str_replace("$up_dir", '', $new_file);//提取图片名字 和 格式
            $image_url = $path."/$year/$img_path";//用作保存的图片路径和名字
            return  $image_url;
        }
    }
}

/**
 * stdClass Object转array
 * @param $array
 * @return array
 */
function object_array($array) {
    if(is_object($array)) {
        $array = (array)$array;
    } if(is_array($array)) {
        foreach($array as $key=>$value) {
            $array[$key] = object_array($value);
        }
    }
    return $array;
}

/**
 * 添加使用时间
 * @param $old
 */
function  add_time($old,$boo){
    $today  =  mktime(0,0,0,date('m'),date('d'),date('Y'));//今天开始时间戳
    $duan   = get_houer();//时间段
    $w       = date('w')? date('w'):7;
    $weeks    = mktime(0,0,0,date('m'),date('d')-$w+1,date('Y'));//周时间戳
    $month    = mktime(0,0,0,date('m'),1,date('Y'));//本月初时间戳
    if($old['pattern_time'] >0){
        $len  = time()-$old['pattern_time'];
        $len =  number_format($len/3600,1);//使用时间
        if($len >0){//切换档位并且该模式使用超过6分钟添加记录
            $time_info['product_id']  = $old['product_id'];
            $time_info['pattern']   = $old['pattern'];
            $time_info['day']  = $today;
            $time_info['duan']  = $duan;
            $time_info['weeks']  = $weeks;
            $time_info['month']  = $month;
            $time_info['length']  = $len;
            $time_info['other']  = $boo;
            $bool4 =  model('time')->save($time_info);
        }
    }

}


/**
 * 上报统计电量
 * @param $old
 */
function  add_electricity($old, $electricity){
    $today  =  mktime(0,0,0,date('m'),date('d'),date('Y'));//今天开始时间戳
    $w       = date('w')? date('w'):7;
    $weeks    = mktime(0,0,0,date('m'),date('d')-$w+1,date('Y'));//周时间戳
    $month    = mktime(0,0,0,date('m'),1,date('Y'));//本月初时间戳
    $tong   = model('Electricity')->where(['product_id'=>$old['product_id'],'day'=>$today])->field('id, num')->find();
    $time_info['product_id']  = $old['product_id'];
    $time_info['day']  = $today;
    $time_info['week']  = $weeks;
    $time_info['month']  = $month;
    $time_info['num']  = $electricity;
    if($tong){//当天有统计
        $time_info['num']  += $tong['num'];
        model('Electricity')->where(['id'=>$tong['id']])->update($time_info);
    }else{
        model('Electricity')->save($time_info);
    }
}

    /**
     * 删除设备组合中的设备
     * @param $product_id //设备ID
     */
    function del_group_product($product_id){
        $user   = model('ProductUser')->where(['product_id'=>$product_id])->field('id, member_id')->select();
        if($user){
            //首先获取控制这台设备的用户 以及 控制表里面的ID
            $member = [];//用户ID
            $productUserId = [];//用户控制ID
            foreach ($user as $a => $b){
                $member[$a] = $b['member_id'];
                $productUserId[$a] = $b['id'];
            }
            $where['product_num']   = ['LIKE','%"'.$product_id.'"%'];
            $where['member_id']     = ['in',$member];
            $list = model('Group')->where($where)->field('group_id, product_num')->select();
            if($list){
                $arr = [];//修改的
                $id  = [];//删除的
                foreach ($list as $k => $v){
                    $newArr  = unserialize($v['product_num']);
                    array_splice($newArr, array_search($product_id,$newArr), 1);//删除数组中的元素,生成新的索引.
                    $num  = count($newArr);
                    if($num){//还有设备就保留
                        $newArr  = serialize($newArr);
                        $arr[$k]['group_id'] = $v['group_id'];
                        $arr[$k]['product_num'] = $newArr;
                        $arr[$k]['num'] = $num;
                    }else{
                        $id[$k] = $v['group_id'];
                    }
                }
                if(!empty($arr)){
                    $bool = model('Group')->saveAll($arr);
                }
                if(!empty($id)){
                    $bool = model('Group')->where(['group_id'=>['in',$id]])->delete(true);
                }
                if(!empty($productUserId)){
                    $bool = model('ProductUser')->where(['id'=>['in',$productUserId]])->delete(true);
                }
                model('productInfo')->where(['product_id'=>$product_id])->delete(true);
                model('productRecord')->where(['product_id'=>$product_id])->delete(true);
            }
        }
      return  $bool;
    }











