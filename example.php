<?php
include (__DIR__.'/Cache/Cache.php');
include (__DIR__.'/settings-example.php');

$Cache = new \Cache\Cache($settings['js']);

if ($Cache->exists('js-files')) {
    $content = $Cache->get('js-files');
} else {
    $content =  file_get_contents('https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
    $content .= file_get_contents('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js');
    $content .= file_get_contents('http://modernizr.com/i/js/modernizr.com-custom-1.6.js');

    if ($custom_time) {
        $Cache->set('js-files', $content, $custom_time);
    } else {
        $Cache->set('js-files', $content);
    }
}

echo $content;
