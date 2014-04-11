<?php

namespace flims\movie;

use flims\MovieId;

class Movie implements IMovie
{
    /**
     * @var \flims\MovieId
     */
    protected $id;
    
    /**
     * @var \flims\movie\IDetail[]
     */
    protected $details = array();
        
        
    /**
     * @param \flims\movie\MovieId $id
     */
    public function __construct(MovieId $id = null)
    {
        $this->id = $id ?: MovieId::fromUuid('');
    }
    
    /**
     * @return \flims\MovieId
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @param $value \flims\MovieId
     * @return \flims\movie\Movie $this
     */
    public function setId($value)
    {
        $this->id = $value;
        return $this;
    }
    
    /**
     * Add a detail on a movie
     * @param \flims\movie\IDetail $details
     * @return \flims\movie\IMovie
     */
    public function addDetail(IDetail $detail)
    {
        $this->details[$detail->getCode()] = $detail;
        return $this;
    }
    
    /**
     * @param string $code
     * @return \flims\movie\IDetail
     */
    public function getDetail($code)
    {
        return isset($this->details[$code]) ? $this->details[$code] : null;
    }
}
