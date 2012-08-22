<?php
$settings = array(
    'api' => array(
        'interface' => 'files',
        'expire' => 360,
        'compress' => true,
        'folder' => (__DIR__.'/cache/api/'),
        'chunk' => 4 // Allow to create a folder tree like 7351/1c98/97ff/b5dc/e916/74d3/79df/b6e3/73511c9897ffb5dce91674d379dfb6e3 instead a file with name 73511c9897ffb5dce91674d379dfb6e3
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
        'folder' => (__DIR__.'/cache/css/'),
        'compress' => true
    ),
    'images' => array(
        'interface' => 'files',
        'expire' => 3600 * 24 * 30
    ),
    'js' => array(
        'interface' => 'files',
        'expire' => 3600 * 24 * 30,
        'folder' => (__DIR__.'/cache/js/'),
        'compress' => true
    )
);
