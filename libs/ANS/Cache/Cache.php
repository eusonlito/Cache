<?php
namespace ANS\Cache;

class Cache
{
    private $Interface;

    private $settings = array(
        'interface' => '',
        'exception' => false
    );

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
        if ($this->loaded() !== true) {
            return false;
        }

        if (method_exists($this->Interface, $method) !== true) {
            if ($settings['exception']) {
                throw new \BadFunctionCallException(sprintf('Method %s doesn\'t exists in Cache Interface', $method));
            } else {
                return false;
            }
        }

        return call_user_func_array(array($this->Interface, $method), $arguments);
    }

    /**
    * public function loaded (void)
    *
    * returns if cache could be loaded
    *
    * return boolean
    */
    public function loaded ()
    {
        return $this->Interface->loaded;
    }

    /**
    * public function setSettings (array $settings)
    *
    * Set the execution settings
    */
    public function setSettings ($settings)
    {
        $settings = array_merge($this->settings, $settings);

        if ($this->settings['interface'] && ($settings['interface'] === $this->settings['interface'])) {
            $response = $this->Interface->setSettings($settings);

            if ($response) {
                return $this->settings = $settings;
            } else {
                return $response;
            }
        }

        if (empty($settings['interface'])) {
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

        return $this->settings = $settings;
    }

    /**
    * public function setSettings (string $key)
    *
    * Set the execution settings
    */
    public function getSettings ($key)
    {
        return $this->settings[$key];
    }
}

if (!spl_autoload_functions()) {
    spl_autoload_register(__NAMESPACE__.'\\Cache::autoload');
}
