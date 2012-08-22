<?php
namespace ANS\Cache;

class Cache
{
    private $settings = array();
    private $Interface;

    /**
     * public function __construct (array $settings)
     *
     * return none
     */
    public function __construct ($settings = array())
    {
        if ($settings) {
            $this->setSettings($settings);
        }
    }

    static function autoload ($class)
    {
        $file = __DIR__.'/'.(str_replace(array(__NAMESPACE__, '\\'), array('', '/'), $class)).'.php';

        if (is_file($file)) {
            include_once ($file);
        }
    }

    /**
     * public function __call (array $settings)
     *
     * Call Interface methods
     *
     * return none
     */
    public function __call ($method, $arguments)
    {
        if (!$this->Interface->loaded) {
            return false;
        }

        if (!method_exists($this->Interface, $method)) {
            if ($settings['exception']) {
                throw new \BadFunctionCallException(sprintf('Method %s doesn\'t exists in Cache Interface', $method));
            } else {
                return false;
            }
        }

        return call_user_func_array(array($this->Interface, $method), $arguments);
    }

    /**
    * public function setSettings (array $settings)
    *
    * Set the execution settings
    */
    public function setSettings ($settings)
    {
        $settings = array_merge($this->settings, $settings);

        if (!isset($this->settings['interface']) || ($settings['interface'] !== $this->settings['interface'])) {
            if (!$settings['interface']) {
                if ($settings['exception']) {
                    throw new \InvalidArgumentException('You must define some cache interface (apc, memcache, memcached, files)');
                } else {
                    return false;
                }
            }

            $class = '\\ANS\\Cache\\Interfaces\\'.ucfirst($settings['interface']);

            try {
                $this->Interface = new $class($settings);
            } catch (\UnexpectedValueException $e) {
                if ($settings['exception']) {
                    throw new \UnexpectedValueException(sprintf('Defined cache interface can\'t be loaded: %s', $e->getMessage()));
                } else {
                    return false;
                }
            }
        }

        $this->settings = $settings;

        $this->Interface->setSettings($settings);
    }

    /**
    * public function setSettings (string $key)
    *
    * Set the execution settings
    */
    public function getSettings ($key)
    {
        if (!$this->Interface->loaded) {
            return false;
        }

        return $this->settings[$key];
    }
}

if (!spl_autoload_functions()) {
    spl_autoload_register(__NAMESPACE__.'\\Cache::autoload');
}
