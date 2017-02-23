<?php

namespace Splash\Bundle\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Object
{
    /** 
     * @var string
     */
    private $class;
    
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
    
    /**
     *  Object Synchronistion Limitations 
     *  
     *  This Flags are Used by Splash Server to Prevent Unexpected Operations on Remote Server
     */
    
    /** @var boolean */
    public  $allow_push_created     =   TRUE;        // Allow Creation Of New Local Objects
    /** @var boolean */
    public  $allow_push_updated     =   TRUE;        // Allow Update Of Existing Local Objects
    /** @var boolean */
    public  $allow_push_deleted     =   TRUE;        // Allow Delete Of Existing Local Objects
    
    /**
     *  Object Synchronistion Recommended Configuration 
     */
    /** @var boolean */
    public  $enable_push_created    =   TRUE;         // Enable Creation Of New Local Objects when Not Existing
    /** @var boolean */
    public  $enable_push_updated    =   TRUE;         // Enable Update Of Existing Local Objects when Modified Remotly
    /** @var boolean */
    public  $enable_push_deleted    =   TRUE;         // Enable Delete Of Existing Local Objects when Deleted Remotly

    /** @var boolean */
    public  $enable_pull_created    =   TRUE;         // Enable Import Of New Local Objects 
    /** @var boolean */
    public  $enable_pull_updated    =   TRUE;         // Enable Import of Updates of Local Objects when Modified Localy
    /** @var boolean */
    public  $enable_pull_deleted    =   TRUE;         // Enable Delete Of Remotes Objects when Deleted Localy
                        
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }
    
    public function getClass()
    {
        return $this->class;
    }
    
    public function getType()
    {
        return $this->type;
    }

    public function getDisabled()
    {
        return $this->disabled;
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

    /**
     *  @abstract   Override Get Description Array for requested Object Type
     * 
     *  @return     array
     */    
    public function getObjectDescription()
    {
        //====================================================================//
        // Build & Return Object Description Array
        return array(
            //====================================================================//
            // General Object definition
            "type"          =>  $this->getType(),                   // Object Type Name
            "name"          =>  $this->getName(),                   // Object Display Neme
            "description"   =>  $this->getDescription(),            // Object Descritioon
            "icon"          =>  $this->getIcon(),                   // Object Icon
            "disabled"      =>  $this->getDisabled(),               // Is This Object Enabled or Not?
            //====================================================================//
            // Object Limitations
            "allow_push_created"      =>    $this->allow_push_created,
            "allow_push_updated"      =>    $this->allow_push_updated,
            "allow_push_deleted"      =>    $this->allow_push_deleted,
            //====================================================================//
            // Object Default Configuration
            "enable_push_created"     =>    $this->enable_push_created,
            "enable_push_updated"     =>    $this->enable_push_updated,
            "enable_push_deleted"     =>    $this->enable_push_deleted,
            "enable_pull_created"     =>    $this->enable_pull_created,
            "enable_pull_updated"     =>    $this->enable_pull_updated,
            "enable_pull_deleted"     =>    $this->enable_pull_deleted
        );
    }       
    
}
