<?php

namespace flims\apiProvider;

use flims\IConfig;

interface IProvider
{
    /**
     * Injects Api config and directory to register to during bootstrap
     * @param \flims\IConfig $conf
     */
    public static function injectConf(IConfig $conf);

    /**
     * Register itself into a directory
     * @param \flims\apiProvider\Directory $directory
     */
    public static function registerToDirectory(Directory $directory);
    
    /**
     * @return string
     */
    public function getCode();
}