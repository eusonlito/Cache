<?php
$settings = array(
    'api' => array(
        'expire' => 360,
        'interface' => 'files',
        'compress' => true
    ),
    'config' => array(
        'expire' => 3600 * 24 * 30,
        'interface' => 'memcache',
        'host' => 'localhost',
        'port' => 11211
    ),
    'db' => array(
        'expire' => 60,
        'interface' => 'apc',
        'exception' => true
    ),
    'css' => array(
        'expire' => 3600 * 24 * 30,
        'interface' => 'files',
        'compress' => true
    ),
    'images' => array(
        'expire' => 3600 * 24 * 30,
        'interface' => 'files'
    ),
    'js' => array(
        'expire' => 3600 * 24 * 30,
        'interface' => 'files',
        'compress' => true
    )
);
