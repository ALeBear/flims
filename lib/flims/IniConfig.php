<?php

namespace flims;

class IniConfig implements IConfig
{
    /**
     * Ini files path (no subdirs)
     * @var string
     */
    protected static $path;
    
    /**
     * @var string
     */
    protected static $env;
    
    /**
     * @var array
     */
    protected $data;
    
    /**
     * @var array 
     */
    protected static $files = array();

    /**
     * Injects the current environment
     * @param string $env
     */
    public static function injectEnv($env)
    {
        self::$env = $env;
    }
    
    /**
     * Injectes the path at bootstrap time
     * @param string $path
     */
    public static function injectPath($path)
    {
        self::$path = $path;
    }
    
    /**
     * Loads a file and return a Config of it
     * @param string $name
     * @return IConfig
     */
    public static function file($name)
    {
        if (!array_key_Exists($name, self::$files)) {
            $filename = sprintf('%s/%s', self::$path, $name);
            if (strtolower(substr($filename, -4)) != '.ini') {
                $filename .= '.ini';
            }
            if (!file_exists($filename)) {
                throw new ConfigException(sprintf('Config file does not exists: %s', $filename));
            }
            if (!($data = @parse_ini_file($filename, true))) {
                throw new ConfigException(sprintf('Config file malformed: %s', $filename));
            }
            static::$files[$name] = new IniConfig($data);
        }
        
        return static::$files[$name];
    }
    
    /**
     * @param array $data
     */
    protected function __construct($data)
    {
        $this->data = $data;
    }
    
    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $separatorPos = strpos($name, '/');
        $section = substr($name, 0, $separatorPos);
        $name = substr($name, $separatorPos ? $separatorPos + 1 : 0);
        
        return strlen($section)
            ? (isset($this->data[$section][$name]) ? $this->data[$section][$name] : $default)
            : (isset($this->data[$name]) ? $this->data[$name] : $default);
    }
    
    /**
     * Get a whole section of vars
     * @return array
     */
    public function getSection($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : array();
    }
}
