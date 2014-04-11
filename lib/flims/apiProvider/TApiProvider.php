<?php


namespace flims\apiProvider;

use flims\IConfig;

trait TApiProvider
{
    /**
     * @var flims\IConfig
     */
    protected static $conf;
    
    /**
     * Injects Api config and directory to register to during bootstrap
     * @param \flims\IConfig $conf
     */
    public static function injectConf(IConfig $conf)
    {
        self::$conf = $conf;
    }

    /**
     * Register itself into a directory
     * @param \flims\apiProvider\Directory $directory
     */
    public static function registerToDirectory(Directory $directory)
    {
        $directory->register(new static());
    }
    
    /**
     * @return \Guzzle\Service\Client
     */
    abstract protected function getGuzzleClient();
    
    /**
     * Is this entry of the given type?
     * @param integer $type
     * @return boolean
     */
    public function hasType($type)
    {
        return $type & $this->getTypes();
    }
    
    /**
     * An entry can have one or more types (usually bit-coded values)
     * @return integer
     */
    public function getTypes()
    {
        return (Directory::ENTRY_TYPE_LISTING * $this instanceof IListingProvider)
            | (Directory::ENTRY_TYPE_MOVIE * $this instanceof IMovieProvider);
    }
    
    /**
     * Get the array of curl options defined in the conf
     * @return array
     */
    protected function getCurlOptions()
    {
        $options = array();
        foreach (self::$conf->getSection('curl', array()) as $key => $val) {
            $options[defined($key) ? constant($key) : $key] = $val;
        }
        return $options;
    }
}