<?php

namespace flims;

interface IConfig
{
    /**
     * Injects the current environment
     * @param string $env
     */
    public static function injectEnv($env);
    
    /**
     * Loads a file/section and return a Config of it
     * @param string $name
     * @return IConfig
     */
    public static function file($name);
    
    /**
     * @param string $name include section like this: section/name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null);
    
    /**
     * Get a whole section of vars
     * @return array
     */
    public function getSection($name);
}
