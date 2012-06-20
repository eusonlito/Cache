Cache
=====

This script works as simple interface with APC, Memcache, Memcached and Files to store data into cache.

You will can store different data into different cache methods (some examples into settings-example.php).

Require PSR-0 autoload compilant app

Examples
--------

#### Files cache

    include (__DIR__.'/Cache/Cache.php');
    include (__DIR__.'/settings-example.php');

    $Cache = new \Cache\Cache($settings['js']);

    if ($Cache->exists('js-files')) {
        $content = $Cache->get('js-files');
    } else {
        $content = myComplexProcess();

        if ($custom_time) {
            $Cache->set('js-files', $content, $custom_time);
        } else {
            $Cache->set('js-files', $content);
        }
    }

#### Database Query cache (into APC)

    include (__DIR__.'/Cache/Cache.php');
    include (__DIR__.'/settings-example.php');

    $Cache = new \Cache\Cache($settings['db']);

    $query = 'SELECT * FROM Users;';
    $cache_key = md5($query);

    if ($Cache->exists($cache_key)) {
        $rows = $Cache->get($cache_key);
    } else {
        $rows = mysql_query($cache_key);

        if ($custom_time) {
            $Cache->set($cache_key, $rows, $custom_time);
        } else {
            $Cache->set($cache_key, $rows);
        }
    }

#### Configuration cache (into Memcache)

    include (__DIR__.'/Cache/Cache.php');
    include (__DIR__.'/settings-example.php');

    $Cache = new \Cache\Cache($settings['config']);

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

        if ($custom_time) {
            $Cache->set($cache_key, $configuration, $custom_time);
        } else {
            $Cache->set($cache_key, $configuration);
        }
    }