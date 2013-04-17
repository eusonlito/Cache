Cache
=====

This script works as simple interface with APC, Memcache, Memcached and Files to store data into cache.

You will can store different data into different cache methods (some examples into settings-example.php).

Examples
--------

#### Files cache

    <?php
    include (__DIR__.'/libs/ANS/Cache/Cache.php');
    include (__DIR__.'/settings-example.php');

    $Cache = new \ANS\Cache\Cache($settings['js']);

    if ($Cache->exists('js-files')) {
        $content = $Cache->get('js-files');
    } else {
        $content =  file_get_contents('https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
        $content .= file_get_contents('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js');
        $content .= file_get_contents('http://modernizr.com/i/js/modernizr.com-custom-1.6.js');

        // Cache expired time is loaded from settings
        // but you can set your own time in seconds from now
        // Third parameter is optional
        $Cache->set('js-files', $content, $custom_time);
    }

#### Database Query cache (into APC)

    <?php
    include (__DIR__.'/libs/ANS/Cache/Cache.php');
    include (__DIR__.'/settings-example.php');

    $Cache = new \ANS\Cache\Cache($settings['db']);

    $query = 'SELECT * FROM Users;';
    $cache_key = md5($query);

    if ($Cache->exists($cache_key)) {
        $rows = $Cache->get($cache_key);
    } else {
        $rows = mysql_fetch_assoc(mysql_query($query));

        // Cache expired time is loaded from settings
        // but you can set your own time in seconds from now
        // Third parameter is optional
        $Cache->set($cache_key, $rows, $custom_time);
    }

#### Configuration cache (into Memcache)

    <?php
    include (__DIR__.'/libs/ANS/Cache/Cache.php');
    include (__DIR__.'/settings-example.php');

    $Cache = new \ANS\Cache\Cache($settings['config']);

    $config_files = array(
        'db', 'paths', 'events', 'routes', 'templates', 'mail',
        'session', 'tables', 'actions', 'css', 'languages'
    );

    $config_key = md5(serialize($config_files));

    if ($Cache->exists($cache_key)) {
        $configuration = $Cache->get($cache_key);
    } else {
        $configuration = array();

        foreach ($config_files as $file) {
            if (is_file(__DIR__.'/config/'.$file.'.php')) {
                $configuration = array_replace_recursive($configuration, include(__DIR__.'/config/'.$file.'.php'));
            }
        }

        // Cache expired time is loaded from settings
        // but you can set your own time in seconds from now
        // Third parameter is optional
        $Cache->set($cache_key, $configuration, $custom_time);
    }