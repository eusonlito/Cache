<?php
namespace Cache\Interfaces;

class Files implements \Cache\Icache
{
    public $loaded = false;

    private $settings = array();
    private $folder;

    /**
     * public function __construct (array $settings)
     *
     * return none
     */
    public function __construct ($settings)
    {
        $this->folder = $settings['folder'];

        if (!$this->folder) {
            if ($settings['exception']) {
                throw new \InvalidArgumentException('You must define base folder to store cache files');
            } else {
                return false;
            }
        }

        $this->folder = preg_replace('#[/\\\]+#', '/', $this->folder.'/');

        if (!is_dir($this->folder)) {
            $base = dirname($this->folder);

            if (!is_writable($base)) {
                if ($settings['exception']) {
                    throw new \UnexpectedValueException('Defined cache folder not exists and can not be created');
                } else {
                    return false;
                }
            }

            mkdir($this->folder, 0700);
        } else if (!is_writable($this->folder)) {
            if ($settings['exception']) {
                throw new \UnexpectedValueException('Defined cache folder is not writable');
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
     * private function fileName (string $file, [int/string $time], [string $flags])
     *
     * returns a filename to save in cache
     *
     * return false/string
     */
    private function fileName ($file, $time = 0, $flags = '')
    {
        if (strpos($file, '://') !== false) {
            $file = parse_url($file, PHP_URL_PATH);
        }

        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $ext = strstr($ext, 'php') ? 'txt' : $ext;

        return $this->folder.md5($file.serialize($flags)).($ext ? ('.'.$ext) : '');
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
        $file = $this->fileName($key);

        return (is_file($file) && (filemtime($file) > time())) ? true : false;
    }

    /**
    * public function set ($key, $value, [$expire integer])
    *
    * Set a variable into memory
    *
    * return mixed
    */
    public function set ($key, $value, $expire = 3600)
    {
        $file = $this->fileName($key);

        $expire = is_integer($expire) ? $expire : $this->settings['expire'];

        if (!is_file($file) || is_writable($file)) {
            file_put_contents($file, $this->settings['compress'] ? gzdeflate(serialize($value)) : serialize($value));

            chmod($file, 0600);

            touch($file, time() + $expire);
        }

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
        $file = $this->fileName($key);

        if (!is_file($file) || (filemtime($file) < time())) {
            return null;
        }

        return unserialize($this->settings['compress'] ? gzinflate(file_get_contents($file)) : file_get_contents($file));
    }

    /**
    * public function delete ($key)
    *
    * Delete a variable from memory
    *
    * return mixed
    */
    public function delete ($key)
    {
        $file = $this->fileName($key);

        return is_file($file) ? unlink($file) : null;
    }

    /**
    * public function clear (void)
    *
    * Clear Files cache
    *
    * return mixed
    */
    public function clear ()
    {
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
        return false;
    }
}
