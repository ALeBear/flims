<?php

namespace flims\movie;

use flims\MovieId;
use flims\IConfig;
use flims\apiProvider\Directory;
use flims\ServiceDirectory;

/**
 * @Entity @Table(name="movie_builder")
 * @HasLifecycleCallbacks
 */
class Builder implements IBuilder
{
    /**
     * @var \flims\IConfig 
     */
    protected static $conf;
    
    /** 
     * @Id @Column(type="integer") @GeneratedValue 
     * @param integer
     */
    protected $id;
    
    /**
     * @Column(type="string", length=32, unique=true, nullable=false)
     * @var string
     */
    protected $name;
    
    /**
     * @Column(type="string", length=800, nullable=false)
     * @var string
     */
    protected $blueprint;
    
    /**
     * @var \flims\movie\IMovie 
     */
    protected $movie;
    
    /**
     * @var string
     */
    protected $language;
    
    /**
     * @var string[] 
     */
    protected $providerCodes = array();
    
    /**
     * Details added, stored for the blueprints
     * contains array like that:
     * array('code' => $detailCode, 'provider' => $providerCode)
     * @var string[][]
     */
    protected $detailsAdded = array();
    
    
    /**
     * Injects Api config and directory to register to during bootstrap
     * @param \flims\IConfig $conf
     */
    public static function injectConf(IConfig $conf)
    {
        self::$conf = $conf;
    }

    /**
     * @param \flims\MovieId $id
     * @return \flims\movie\IBuilder
     */
    public static function start(MovieId $id)
    {
        return new static(new Movie($id));
    }
    
    /**
     * Returns a fully configured builder from a name. Can still be
     * configured more if needed
     * WARNING! Will return a fresh new Builder, not the one in DB exactly.
     * @param \flims\MovieId $id
     * #param string $name
     * @return \flims\movie\IBuilder
     */
    public static function fromName(MovieId $id, $name)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = ServiceDirectory::getInstance()->locate('EntityManager')->getConcreteService();
        return static::fromBlueprint($id, $em->getRepository(__CLASS__)->findOneByName($name)->blueprint);
    }
    
    /**
     * Returns a fully configured builder from a blueprint. Can still be
     * configured more if needed
     * @param \flims\MovieId $id
     * #param string $blueprint
     * @return \flims\movie\IBuilder
     */
    public static function fromBlueprint(MovieId $id, $blueprint)
    {
        $decoded = @json_decode($blueprint, true);
        if (!$decoded) {
            throw new InvalidBlueprintException(sprintf('Blueprint not valid JSON: ' . $blueprint));
        }
        if (!isset($decoded['details']) || !is_array($decoded['details'])) {
            throw new InvalidBlueprintException(sprintf('Blueprint does not contain the minimum fields: ' . $blueprint));
        }
        
        $class = isset($decoded['builderclass']) ? $decoded['builderclass'] : __CLASS__;
        
        /* @var $builder \flims\movie\IBuilder */
        $builder = new $class(new Movie($id));
        
        if (isset($decoded['language']) && $decoded['language']) {
            $builder->setLanguage($decoded['language']);
        }
        if (isset($decoded['name']) && $decoded['name']) {
            $builder->setName($decoded['name']);
        }
        if (isset($decoded['providers']) && is_array($decoded['providers'])) {
            foreach ($decoded['providers'] as $providerCode) {
                $builder->addProvider($providerCode);
            }
        }
        foreach ($decoded['details'] as $detailArray) {
            $builder->addDetail($detailArray['code'], isset($detailArray['provider']) ? $detailArray['provider'] : null);
        }
        
        return $builder;
    }

    /**
     * ISO 639 ("en") or IETF ("en-US") language code
     * @param type $langCode
     * @return \flims\movie\IBuilder $this
     */
    public function setLanguage($langCode)
    {
        $this->language = $langCode;
        return $this;
    }
    
    /**
     * Adds a provider for the details
     * @param string $code
     * @return \flims\movie\IBuilder $this
     */
    public function addProvider($code)
    {
        $this->providerCodes[] = $code;
        $this->providerCodes = array_unique($this->providerCodes);
        
        return $this;
    }
    
    /**
     * Adds a detail type needed, and force its provider if wanted. This will
     * immediately trigger the retrieval of the detail
     * @param string $detailCode
     * @param string $providerCode
     * @return \flims\movie\IBuilder $this
     */
    public function addDetail($detailCode, $providerCode = null)
    {
        $this->detailsAdded[] = array('code' => $detailCode, 'provider' => $providerCode);
        
        return $this;
    }
    
    /**
     * @return string JSON-encoded blueprint storable
     */
    public function getBlueprint()
    {
        return json_encode(array(
            'builderclass' => get_class($this),
            'name' => $this->getName(),
            'language' => $this->language,
            'providers' => $this->providerCodes,
            'details' => $this->detailsAdded));
    }
    
    /**
     * @return \flims\movie\Movie
     */
    public function end()
    {
        return $this->fillMovie();
    }
    
    /**
     * Fills all the details of a movie
     * @return flims\movie\IMovie
     */
    protected function fillMovie()
    {
        $providers = array();
        foreach (array_unique($this->providerCodes) as $pc) {
            $provider = $this->getProviderFromCode($pc);
            if ($provider->providesForLanguage($this->getLanguage())) {
                $providers[$pc] = $this->getProviderFromCode($pc);
            }
        }
        
        $detailsByProvider = array();
        foreach ($this->detailsAdded as $detailArr) {
            if ($detailArr['provider']) {
                if (!isset($providers[$detailArr['provider']])) {
                    $providers[$detailArr['provider']] = $this->getProviderFromCode($detailArr['provider']);
                }
                isset($detailsByProvider[$detailArr['provider']]) || $detailsByProvider[$detailArr['provider']] = array();
                $detailsByProvider[$detailArr['provider']][] = $detailArr['code'];
            } else {
                foreach ($providers as $provider) {
                    if ($provider->provides($detailArr['code'])) {
                        isset($detailsByProvider[$provider->getCode()]) || $detailsByProvider[$provider->getCode()] = array();
                        $detailsByProvider[$provider->getCode()][] = $detailArr['code'];
                        break;
                    }
                }
            }
        }
        
        foreach ($detailsByProvider as $providerCode => $detailCodes) {
            $providers[$providerCode]->fillMovie($this->getLanguage(), $this->movie, $detailCodes);
        }
        
        return $this->movie;
    }
    
    /**
     * @return string
     */
    protected function getLanguage()
    {
        return $this->language ?: self::$conf->get('display/default_language');
    }
    
    /**
     * @param \flims\movie\IMovie $movie
     */
    protected function __construct(IMovie $movie)
    {
        $this->movie = $movie;
    }
    
    /**
     * Instantiate a provider from its code
     * @param string $code
     * @return \flims\apiProvider\IMovieProvider
     */
    protected function getProviderFromCode($code)
    {
        return Directory::getInstance()->locate($code);
    }

    /**
     * @param string $value
     * @return \flims\movie\Builder $this
     */
    public function setName($value)
    {
        $this->name = $value;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @PrePersist
     * @PreUpdate
     */
    public function preSave()
    {
        $this->blueprint = $this->getBlueprint();
    }
}
