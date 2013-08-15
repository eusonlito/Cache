<?php
namespace ANS\Cache\Interfaces;

class Files implements \ANS\Cache\Icache
{
    public $loaded = false;

    private $folder;
    private $reload = false;

    private $settings = array(
        'folder' => '',
        'exception' => false,
        'chunk' => 0,
        'compress' => true,
        'expire' => 2592000
    );

    /**
     * public function __construct (array $settings)
     *
     * return none
     */
    public function __construct ($settings)
    {
        $this->folder = $settings['folder'];

        if (empty($this->folder)) {
            if ($settings['exception']) {
                throw new \InvalidArgumentException('You must define base folder to store cache files');
            } else {
                return false;
            }
        }

        $this->folder = preg_replace('#[/\\\]+#', '/', $this->folder.'/');

        if (!is_dir($this->folder)) {
            $created = @mkdir($this->folder, 0700, true);

            if ($created !== true) {
                if ($settings['exception']) {
                    throw new \UnexpectedValueException('Defined cache folder can not be created');
                } else {
                    return false;
                }
            }
        } else if (!is_writable($this->folder)) {
            if ($settings['exception']) {
                throw new \UnexpectedValueException('Defined cache folder is not writable');
            } else {
                return false;
            }
        }

        $this->loaded = true;

        return $this->setSettings($settings);
    }

    /**
    * public function setSettings (array $settings)
    *
    * Set the execution settings
    */
    public function setSettings (array $settings)
    {
        return $this->settings = array_merge($this->settings, $settings);
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
        $ext = $ext ? ('.'.$ext) : '';

        $file = md5($file.serialize($flags));

        if ($this->settings['chunk']) {
            $file = chunk_split($file, $this->settings['chunk'], '/').$file;
        }

        return $this->folder.$file.$ext;
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
        if (empty($this->loaded) || $this->reload) {
            return false;
        }

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
    public function set ($key, $value, $expire = 0)
    {
        if (empty($this->loaded)) {
            return false;
        }

        $file = $this->fileName($key);
        $dir = dirname($file);

        if ($this->settings['chunk'] && !is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (!is_file($file) || is_writable($file)) {
            file_put_contents($file, $this->settings['compress'] ? gzdeflate(serialize($value)) : serialize($value));

            chmod($file, 0600);

            touch($file, time() + ($expire ?: $this->settings['expire']));
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
        if (empty($this->loaded)) {
            return false;
        }

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
        if (empty($this->loaded)) {
            return false;
        }

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
        if (empty($this->loaded)) {
            return false;
        }

        $file = $this->fileName($key);

        return is_file($file) ? filemtime($file) : null;
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
