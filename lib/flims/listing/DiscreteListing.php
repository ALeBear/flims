<?php

namespace flims\listing;

/**
 * Listing comprised of pointers to movies by ids provided by an Api provider,
 * but not dynamic and hence can be stored in the DB
 * @Entity
 */
class DiscreteListing extends ManualListing
{
    /**
     * Maximum number of ids to get from the provider
     */
    const MAX_IDS_TO_GET = 9999;
    
    
    /**
     * @Column(type="string", length=3000)
     * @var string
     */
    protected $definition;
    
    
    /**
     * @param integer $start
     * @param integer $offset
     * @return \flims\MovieId[]
     */
    public function getMovieIds($offsetStart = 0, $offsetLength = self::DEFAULT_LENGTH)
    {
        if (!is_array($this->movieIds)) {
            $this->rebuild();
        }
        
        return parent::getMovieIds($offsetStart, $offsetLength);
    }
    
    /**
     * Rebuilds the list from the DynamicListing output
     */
    public function rebuild()
    {
        $this->movieIds = DynamicListing::fromDefinition($this->definition)->getMovieIds(0, static::MAX_IDS_TO_GET);
    }
}