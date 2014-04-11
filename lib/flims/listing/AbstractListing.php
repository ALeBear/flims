<?php

namespace flims\listing;

/**
 * @Entity @Table(name="listing")
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="discr", type="string", length=30)
 * @DiscriminatorMap({"manual" = "ManualListing", "discrete" = "DiscreteListing", "dynamic" = "DynamicListing"})
 */
abstract class AbstractListing implements IListing
{
    /** 
     * @Id @Column(type="integer") @GeneratedValue 
     * @param integer
     */
    protected $id;
    
    /** 
     * @Column(type="string")
     * @param string
     */
    protected $name;
    
    
    /**
     * @return string
     */
    public function __toString()
    {
        echo sprintf('%s: %s', substr(__CLASS__, strrpos(__CLASS__, '\\') + 1), $this->name);
    }
    
    /**
     * @param string $value
     * @return \flims\listing\AbstractListing
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
}