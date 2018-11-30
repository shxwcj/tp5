<?php
namespace app\admin\controller;
use app\admin\controller\AdminBase;
use extend\File;
/**
*
*/
class Refresh extends AdminBase
{
    public function index(){
        $path['all']               = RUNTIME_PATH;
        $path['html']              = RUNTIME_PATH.'/temp';
        $path['static']            =  './static/cache';
        $path['cache']             = CACHE_PATH;
        $path['log']               = RUNTIME_PATH.'/log';
        foreach ($path as $k => $v) {
            if (!is_dir($path[$k])) {
                mkdir($path[$k],0777,true);
            }
            $dir_info[$k]['size']  = File::get_unit(sprintf('%.2f',File::dir_size($v)));
            $dir_info[$k]['count'] = File::file_count($v);
        }

        if (request()->isAjax()) {
            $data                  = input();
            !array_key_exists($data['type'], $path) && Api()->setApi('msg','缓存更新失败')->apiError();
            File::del_dir_recursive($path[$data['type']]);
            Api()->setApi('msg','缓存已更新')->apiSuccess();
        }
        return view('', ['dir_info'=>$dir_info]);
    }
}