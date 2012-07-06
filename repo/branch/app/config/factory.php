<?php
$config_files = array('database', 'memcache', 'revision');
$db_port = true;
$cache_host = true;
$cache_port = true;
foreach ($config_files as $config_file) {
    $config = @parse_ini_file(realpath(dirname(__FILE__) . '/../../../../') . '/conf/'. $config_file);
    if ($config) {
        foreach ($config as $key => $value) {
            $container->setParameter($key, $value);
        }
        if(!isset($config['DB_PORT']) && $db_port) $db_port = false;
        if(!isset($config['CACHE_HOST']) && $cache_host) $cache_host = false;
        if(!isset($config['CACHE_PORT']) && $cache_port) $cache_port = false;
    }
}

// Default value
if(false == $db_port) {
    $container->setParameter('db_port', '5432');
}
if(false == $cache_host) {
    $container->setParameter('cache_host', 'localhost');
}
if(false == $cache_port) {
    $container->setParameter('cache_port', '11211');
}

