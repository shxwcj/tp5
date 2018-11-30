<?php

// 所有配置
$config             = @include APP_PATH.'/common/config/config.php';
$config['database'] = @include APP_PATH.'/common/config/database.php';
$custom_config         = @include APP_PATH.'/common/config/custom.php';
$web_config         = @include CACHE_PATH.'/config.cache.php';

return array_merge($config,$custom_config,(array)$web_config);

// return $config;
