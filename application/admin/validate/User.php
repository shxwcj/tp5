<?php
namespace app\common\validate;
use think\Validate;

class User extends Validate{
	 protected $rule = [
        ['nickname', 'require'],
        ['username', 'require'],
    ];
    protected $scene = [
        'add'   =>  ['nickname,username'],
        'edit'	=>	['nickname,username'],	 
    ];    
}