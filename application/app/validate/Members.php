<?php

/**
 * Created by PhpStorm.
 * User: THINK
 * Date: 2018/4/9
 * Time: 11:41
 */
namespace app\app\validate;
use think\Validate;
class Members extends Validate
{
    protected $rule = [
        ['account',      ['regex'=>'/^1[3|4|5|7|8][0-9]{9}$/','unique:Members','require'],                        '帐号格式不正确|帐号已存在|帐号不能为空'],
        ['password',    'require',                          '密码不能为空'],
    ];
    protected $scene = [
        'add'   =>  ['account','password'],
    ];
}