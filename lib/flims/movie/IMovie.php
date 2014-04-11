<?php

namespace flims\movie;

use flims\MovieId;

interface IMovie
{
    /**
     * @return \flims\MovieId
     */
    public function getId();
    
    /**
     * @param $value \flims\MovieId
     * @return \flims\movie\Movie $this
     */
    public function setId($value);
    
    /**
     * Add a detail on a movie
     * @param \flims\movie\IDetail $details
     * @return \flims\movie\IMovie
     */
    public function addDetail(IDetail $detail);
    
    /**
     * @param string $code
     * @return \flims\movie\IDetail
     */
    public function getDetail($code);
}
