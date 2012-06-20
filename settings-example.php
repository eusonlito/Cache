<?php
$settings = array(
    'api' => array(
        'interface' => 'files',
        'expire' => 360,
        'compress' => true
    ),
    'config' => array(
        'interface' => 'memcache',
        'expire' => 3600 * 24 * 30,
        'host' => 'localhost',
        'port' => 11211
    ),
    'db' => array(
        'interface' => 'apc',
        'expire' => 60,
        'exception' => true
    ),
    'css' => array(
        'interface' => 'files',
        'expire' => 3600 * 24 * 30,
        'folder' => (__DIR__.'/cache/'),
        'compress' => true
    ),
    'images' => array(
        'interface' => 'files',
        'expire' => 3600 * 24 * 30
    ),
    'js' => array(
        'interface' => 'files',
        'expire' => 3600 * 24 * 30,
        'folder' => (__DIR__.'/cache/'),
        'compress' => true
    )
);
