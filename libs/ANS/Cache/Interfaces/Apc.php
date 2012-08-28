<?php
namespace ANS\Cache\Interfaces;

if (!function_exists('apc_exists')) {
    function apc_exists ($key)
    {
        return apc_fetch($key) ? true : false;
    }
}

class Apc implements \ANS\Cache\Icache
{
    public $loaded = false;

    private $reload = false;

    private $settings = array(
        'exception' => false,
        'expire' => 2592000
    );

    /**
     * public function __construct ([array $settings])
     *
     * return none
     */
    public function __construct ($settings = array())
    {
        if (!extension_loaded('apc')) {
            if ($settings['exception']) {
                throw new \UnexpectedValueException('PHP-APC extension is not loaded');
            } else {
                return false;
            }
        }

        $this->loaded = true;

        $this->setSettings($settings);
    }

    /**
    * public function setSettings (array $settings)
    *
    * Set the execution settings
    */
    public function setSettings (array $settings)
    {
        $this->settings = array_merge($this->settings, $settings);
    }

    /**
    * public function exists ($key)
    *
    * Check variable exists in memory
    *
    * return boolean
    */
    public function exists ($key)
    {
        if ($this->reload) {
            return false;
        }

        return apc_exists($key);
    }

    /**
    * public function set ($key, $value, [$expire integer])
    *
    * Set a variable into memory
    *
    * return mixed
    */
    public function set ($key, $value, $expire = 0)
    {
        apc_store($key, base64_encode(gzdeflate(serialize($value))), ($expire ?: $this->settings['expire']));

        return $value;
    }

    /**
    * public function get ($key)
    *
    * Get a variable from memory
    *
    * return mixed
    */
    public function get ($key)
    {
        $value = apc_fetch($key);

        if (!$value) {
            return '';
        }

        $value = @gzinflate(base64_decode($value));

        return $value ? unserialize($value) : '';
    }

    /**
    * public function delete ($key)
    *
    * Delete a variable from memory
    *
    * return boolean
    */
    public function delete ($key)
    {
        return apc_delete($key);
    }

    /**
    * public function clear (void)
    *
    * Clear APC cache
    *
    * return mixed
    */
    public function clear ()
    {
        apc_clear_cache();
        apc_clear_cache('user');
        apc_clear_cache('opcode');

        return true;
    }

    /**
    * public function expire ($key)
    *
    * Return the time to expire a key
    *
    * return mixed
    */
    public function expire ($key)
    {
        $info = apc_cache_info('user');

        if (!$info['cache_list']) {
            return false;
        }

        foreach ($info['cache_list'] as $entry) {
            if ($entry['info'] !== $key) {
                continue;
            }

            if ($entry['ttl'] == 0) {
                return 0;
            }

            return $entry['creation_time'] + $entry['ttl'];
        }

        return false;
    }

    /**
    * public function reload (void)
    *
    * Allow to skip a cache read and store it again
    *
    * return mixed
    */
    public function reload ()
    {
        $this->reload = true;
    }
}
