<?php

namespace flims\movie;

abstract class AbstractDetail implements IDetail
{
    /**
     * @var string
     */
    protected $code;
    
    /**
     * @var string
     */
    protected $language;
    
    /**
     * @param type $code
     * @param type $language
     * @return \flims\movie\AbstractDetail
     */
    public static function factory($code, $language)
    {
        return new static($code, $language);
    }
    
    /**
     * @param string $code
     * @param string $language
     */
    protected function __construct($code, $language)
    {
        $this->code = $code;
        $this->language = $language;
    }
    
    /**
     * @return string The language code used to define the detail
     */
    public function getLanguage()
    {
        return $this->language;
    }
    
    /**
     * @return string The code of the details
     */
    public function getCode()
    {
        return $this->code;
    }
}