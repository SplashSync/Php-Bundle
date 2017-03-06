<?php

namespace Splash\Bundle\Annotation;

use Splash\Core\SplashCore as Splash;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Widget
{
    use ContainerAwareTrait;
    
    /** 
     * @var string
     */
    private $class;
    
    /** 
     * @var string
     */
    public $target;
    
    /** 
     * @var string
     * @Required 
     */
    public $type;
    
    /** @var boolean */
    public $disabled        = False;
    
    /** 
     * @var string 
     * @Required 
     */
    public $name;
    
    /** @var string */
    public $description     = 'No Object Description Given';

    /** @var string */
    public $icon            = "fa fa-cubes";
    
    /** @var array */
    public $options         = array();
    
    /** @var array */
    public $parameters      = array();
    
    public function setClass($class)
    {
        //====================================================================//
        // If no Target Class defined, use Splash Entity Class 
        if ( is_null($this->target) ) {
            $this->target = $class;
        } 

        //====================================================================//
        // Init Local Widget Class
        if (class_exists($this->target)) {
            $this->class = new $this->target();
        }

        return $this;
    }
    
    public function getClass()
    {
        return $this->class;
    }
    
    public function getTargetClass()
    {
        return $this->target;
    }   
    
    public function getType()
    {
        return $this->type;
    }

    public function getDisabled()
    {
        return $this->disabled;
    }
    
    public static function getIsDisabled()
    {
        return False;
    }
    
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function getIcon()
    {
        return $this->icon;
    }
    
    public function hasTargetFunction($Function)
    {
        return method_exists($this->target, $Function);
    }
    
    /**
     *  @abstract   Get Definition Array for requested Widget Type
     * 
     *  @return     array
     */    
    public function Description()
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);  
        
        //====================================================================//
        // Build & Return Widget Description Array
        return array(
            //====================================================================//
            // General Object definition
            "type"          =>  $this->getType(),                   // Widget Type Name
            "name"          =>  $this->getName(),                   // Widget Display Neme
            "description"   =>  $this->getDescription(),            // Widget Descritioon
            "icon"          =>  $this->getIcon(),                   // Widget Icon
            "disabled"      =>  $this->getIsDisabled(),             // Is This Widget Enabled or Not?
            //====================================================================//
            // Widget Default Options
            "options"       =>  $this->Options(),                   // Widget Default Options Array
            //====================================================================//
            // Widget Parameters
            "parameters"    =>  $this->Parameters(),                // Widget Default Options Array
        );
    }
    
    /**
     *      @abstract   Return Widget Defaults Options
     */
    public function Options()
    {
        if ( $this->hasTargetFunction("Options") ) {
            return $this->class->Options($this->container);
        } 
        return $this->options;
    }
    
    /**
     *      @abstract   Return Widget Customs Parameters
     */
    public function Parameters()
    {
        if ( $this->hasTargetFunction("Parameters") ) {
            return $this->class->Parameters($this->container);
        } 
        return $this->parameters;
    }        

    /**
     *      @abstract   Return Widget Contents
     */
    public function Get($Parameters =  array())
    {
        if ( $this->hasTargetFunction("Get") ) {
            return $this->class->Get($Parameters, $this->container);
        } 
        return array();
    }        
    
}
