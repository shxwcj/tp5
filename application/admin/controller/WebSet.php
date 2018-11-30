<?php
/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/3/13
 * Time: 9:59
 */

namespace app\admin\controller;
use extend\Upload;
use think\Request;
use think\Image;
use think\Session;
use extend\Encrypt;
/**
 * 网站设置类
 * Created by PhpStorm.
 * @author pengqing
 * User: THINK
 * Date: 2018/3/13
 * Time: 9:59
 */

class WebSet extends AdminBase
{
    /**
     * 网站资料详情
     * @return array
     */
    public function index ()
    {
        $web                    = model('web')->getWeb();//获取网站基本信息
        $areas                  = model('areas')->getAreas($web);//省市区列表
        $web['oldImage']       = $web['logo'];//原图；用于修改
        $web['logo']           = head_img($web['logo']);//检测图片是否存在
        if(request()->isAjax()){
            $data               = input();
            if(!empty($data['logo'])){
                $data['logo']      =  uploadBase64($data['logo'],'admin/static');
                $yuan = $data['oldImage'];//原图
            }else{
                unset($data['logo']);
            }
            unset($data['oldImage']);
            $bool               = model('web')->save($data,['id'=>1]);
            if($bool){//删除原图
                $msg['status'] = 1;
                if(isset($yuan)){
                    $msg['img'] = $data['logo'];
                    if(file_exists('./upload/'.$yuan)){
                        unlink('./upload/'.$yuan);
                    }
                }else{
                    $msg['img'] = false;
                }
                return $msg;
            }
            return false;
        }
        return view([
            'web'               => $web,
            'areas'         => $areas,
        ]);
    }

    /**
     * 通过area_id 获取下级城市
     * return array
     */
    public function getAreas()
    {
        if(request()->isAjax()){
            $areaId              = input('areaId');
            $areas               = model('areas')->getGunior($areaId);//获取下级城市
            return $areas;
        }
    }


    /**
     * 石墨烯协议
     * @return \think\response\View
     */
    public  function agreement(){
        if(request()->isAjax()){
            $data               = input();
            $bool               = model('web')->save(['agreement'=>$data['agreement']],['id'=>1]);
            $return['status'] = 0;
            if($bool){//删除原图
                $return['status'] = 1;
            }
            return  $return;
        }else{
            $agreement  = model('web')->field('agreement')->find();
            return view([
                'agreement'   => $agreement['agreement'],
            ]);
        }
    }





}