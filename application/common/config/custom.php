<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/26 0026
 * Time: 下午 1:52
 */
return[

    'enclosure_path'=>ROOT_PATH.DS.'public' . DS .'upload'. DS . 'enclosure',//上传附件路径
    'images_url'=>DS.'public' . DS .'upload'. DS . 'enclosure',//上传附件路径
    'show_enclosure'=>DS.'public' . DS .'upload'. DS . 'enclosure',//上传附件路径
    'container_type'=>array(1=>'弹簧机',2=>'格子机'),
    'resolution'=>array(1=>'1024*600',2=>'1920*1080'),//显示屏分辨率
    'system'=>array(1=>'Android',2=>'Windows',3=>'Linux'),//控制端适配系统
    'direction'=>array(1=>'竖',2=>'横'),//显示屏方向
    'goods_upload_path'=> DS.'public' . DS .'upload'. DS . 'goods',//上传商品图片路径
];