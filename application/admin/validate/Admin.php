<?php
namespace app\admin\validate;
use think\Validate;

class Admin extends Validate{
	 protected $rule = [
        ['account',      ['unique:Admin','require'],                        '帐号已存在|帐号不能为空'],
        ['nickname',     'require',                    '昵称不能为空'],
        ['password',    'require',                          '密码不能为空'],
         ['department', 'require',                          '填写部门名称'],
      /*  ['phone',       ['regex'=>'/^1[3|4|5|7|8][0-9]{9}$/','unique:Admin','require'],    '手机格式错误|手机号已存在|手机号不能为空'],
        ['email',       'email|unique:Admin|require',                       '邮箱格式错误|邮箱已存在|邮箱不能为空'],*/
        
    ];
    protected $scene = [
        'add'   =>  ['account','nickname','password','department'],
        'edit'	=>	['nickname','department'],
    ];    
}