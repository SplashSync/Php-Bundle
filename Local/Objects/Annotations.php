<?php
/*
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @abstract    Local Overiding Objects Manager for Splash Bundle
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace Splash\Local\Objects;

use Splash\Core\SplashCore  as Splash;
use Splash\Models\ObjectBase;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use Doctrine\ODM\DocumentManager;

class Annotations extends ObjectBase
{
    /*
     * @abstract    Doctrine Entity Manager
     * @var         \Doctrine\ORM\EntityManager
     */
    private $_em = Null;

    /*
     * @abstract    Doctrine Entity Manager
     * @var         \Doctrine\ODM\DocumentManager
     */
    private $_dm = Null;
    
    /*
     * @abstract    Static Objects Class List
     * @var array
     */
    private $_static    = Array();
    
    //====================================================================//
    // Annotation Cache Variables	
    //====================================================================//
    
    private $_objects    =   Null;
    private $_fields     =   array();
    
    //====================================================================//
    // Class Constructor
    //====================================================================//
        
    /**
     *      @abstract       Class Constructor
     */
    function __construct(EntityManager $EntityManager = Null, DocumentManager $DocumentManager = Null, $Objects = array() ) 
    {
        //====================================================================//
        // Store Link to Doctrine Entity Manager Services
        $this->_em = $EntityManager;
        //====================================================================//
        // Store Link to Doctrine Docmument Manager Services
        $this->_dm = $DocumentManager;
        //====================================================================//
        // Store List of Static Objects
        $this->_static = $Objects;
    }    
    
    /**
     *      @abstract   Analyze Annotations & return Objects Types List 
     */
    public function getObjectsTypes()
    {
        $ObjectsTypes   =   [];
        //====================================================================//
        // Load Objects Annotations
        $Annotations = is_null($this->_objects) ? $this->getObjectsAnnotations() : $this->_objects;
        //====================================================================//
        // Walk on all entities
        foreach ($Annotations as $ObjectAnnotation) {
            
            //====================================================================//
            // Splash Object is Disabled
            if ($ObjectAnnotation->getDisabled()) {
                continue;
            }
            
            $ObjectsTypes[] = $ObjectAnnotation->getType();  
        }    
        return $ObjectsTypes;
    }   
    
    /**
     * @abstract    Detect Object Type from Object Local Class
     *              This function is only used internaly to identify if an object is Mapped or Not for Splash
     *       
     * @param   string      $ClassName      Local Object Class Name
     * 
     * @return  string      $ObjectType     Local Object Splash Type Name or Null if not Mapped 
     */
    public function getObjectType($ClassName)
    {
        //====================================================================//
        // Load Objects Annotations
        $Annotations = is_null($this->_objects) ? $this->getObjectsAnnotations() : $this->_objects;
        //====================================================================//
        // Walk on all entities
        foreach ($Annotations as $ObjectAnnotation) {
            
            //====================================================================//
            // Splash Object is Disabled
            if ($ObjectAnnotation->getDisabled()) {
                continue;
            }
            //====================================================================//
            // Splash Object hasn't the Right Class
            if ( $ClassName !== $ObjectAnnotation->getTargetClass()) {
                continue;
            }
            return $ObjectAnnotation->getType();
        }    
        return Null;
    }
    
    /**
     *  @abstract   Analyze Annotations & return Objects Description Array
     * 
     *  @param  string  $ObjectType     Object Type Name
     * 
     */
    public function getObjectDescription($ObjectType)
    {
        //====================================================================//
        // Load Object Annotations
        $ObjectAnnotation = $this->getObjectsAnnotations($ObjectType);
        if (!$ObjectAnnotation) {
            return array();
        }
        //====================================================================//
        // Return Object description Array
        return $ObjectAnnotation->getObjectDescription();
    }       
    
    /**
     *  @abstract   Analyze Annotations & return Objects Fields Array
     * 
     *  @param  string  $ObjectType     Object Type Name
     * 
     */
    public function getObjectFields($ObjectType)
    {
        //====================================================================//
        // Init response
        $Response = [];
        //====================================================================//
        // Load Fields Annotations
        foreach ($this->getFieldsAnnotations($ObjectType) as $Annotation) {
            $Response[] = $Annotation->getDefinition();
        }
        //====================================================================//
        // Publish Fields
        return $Response;
    }   
    
    /**
     *  @abstract   Return a Single Field Annotation using Field Id
     * 
     *  @param  string  $ObjectType     Object Type Name
     * 
     */
    public function getObjectFieldAnnotation($ObjectType,$FieldId)
    {
        //====================================================================//
        // Filter Fields Annotations List
        $Array  =   $this->getObjectFieldsAnnotations($ObjectType, ["id" => $FieldId]);
        //====================================================================//
        // Check Field Id is Unique
        if (count($Array) !== 1) {
            return Null;
        }
        //====================================================================//
        // Return Field Annotations
        return array_shift($Array);
    } 
    
    /**
     *  @abstract   Return List of Listed Fields Annotation with filtering
     * 
     *  @param  string  $ObjectType     Object Type Name
     * 
     */
    public function getObjectFieldsAnnotations($ObjectType,$Filters = array())
    {
        //====================================================================//
        // Init response
        $Response = [];
        //====================================================================//
        // Load Fields Annotations
        foreach ($this->getFieldsAnnotations($ObjectType) as $Name => $Annotation) {
            $Filtered = False;
            foreach ($Filters as $Key => $Value) {
                //====================================================================//
                // Check Field Annotation Values
                if ( $Annotation->getProperty($Key) !== $Value ) {
                    $Filtered = True;
                }
            }
            //====================================================================//
            // Skipp Filetered Field
            if ( $Filtered ) {
                continue;
            }
            $Response[$Name] = $Annotation;
        }
        //====================================================================//
        // Publish Fields
        return $Response;
    }    
    
    /**
     *  @abstract   Return List of Object Available Fields Lists  
     * 
     *  @param  string  $ObjectType     Object Type Name
     * 
     */
    public function getObjectListsNamesArray($ObjectType)
    {
        //====================================================================//
        // Init response
        $Response = [];
        //====================================================================//
        // Load Fields Annotations
        foreach ($this->getFieldsAnnotations($ObjectType) as $Name => $Annotation) {
            
            //====================================================================//
            // Check if Field is a List Field
            if ( !($ListName = self::ListField_DecodeListName($Annotation->getId())) ) {
                continue;
            }
            //====================================================================//
            // Add List to Response Array
            if ( !in_array($ListName, $Response) ) {
                $Response[] = $ListName;
            }
        }
        //====================================================================//
        // Return List Array
        return $Response;
    }      
    
    /**
     *  @abstract   Analyze Annotations & return Objects Annotations List
     *  
     *  @param  string  $ObjectType     Filter on a Specific Type Name
     * 
     */
    public function getObjectsAnnotations($ObjectType = Null)
    {
        //====================================================================//
        // Load Objects Annotations
        if ( is_null($this->_objects) ) {
            $this->loadObjectsAnnotations();
        }
        
        //====================================================================//
        // If NO Specific Type was requested
        if ( is_null($ObjectType)) {
            return $this->_objects;
        }
        
        //====================================================================//
        // If Specific Type was requested but not found
        if ( !is_null($ObjectType) && !isset($this->_objects[$ObjectType])) {
            return Null;
        }
        return $this->_objects[$ObjectType];
        
    }
    
    /**
     *  @abstract   Analyze Annotations & return Objects Fields Annotations List
     *  
     *  @param  string  $ObjectType     Filter on a Specific Type Name
     */
    private function getFieldsAnnotations($ObjectType)
    {
        //====================================================================//
        // Load Object Fields Annotations if Not Already Done
        if ( !isset($this->_fields[$ObjectType]) ) {
            $this->loadFieldsAnnotations($ObjectType);
        }
        
        //====================================================================//
        // Return Object Fields Annotations
        return $this->_fields[$ObjectType];
    }
    
    /**
     *  @abstract   Analyze Annotations
     */
    private function loadObjectsAnnotations()
    {
        //====================================================================//
        // Init Result Array
        $this->_objects = [];
        //====================================================================//
        // Load Static Annotations
        //====================================================================//
        if ($this->_em && !empty($this->_static)) {
            //====================================================================//
            // Walk on all entities
            foreach ($this->_static as $ObjectClass) {            
                $this->loadObjectAnnotation($ObjectClass, $this->_em);
            }
        } 
        
        //====================================================================//
        // Load Doctrine ORM Annotations
        //====================================================================//
        if ($this->_em) {
            //====================================================================//
            // Load Doctrine Metadata
            $MetaData = $this->_em->getMetadataFactory()->getAllMetadata();
            //====================================================================//
            // Walk on all entities
            foreach ($MetaData as $EntityData) {            
                $this->loadObjectAnnotation($EntityData->getName(), $this->_em);
            }
        } 
        
        //====================================================================//
        // Load Doctrine ODM Annotations
        //====================================================================//
        if ($this->_dm) {
            //====================================================================//
            // Load Doctrine Metadata
            $MetaData = $this->_dm->getMetadataFactory()->getAllMetadata();
            //====================================================================//
            // Walk on all entities
            foreach ($MetaData as $EntityData) {            
                $this->loadObjectAnnotation($EntityData->getName(), $this->_dm);
            }
        } 
        
        return $this->_objects;
    } 
    
    /**
     * @abstract   Analyze & Load Object Class Annotations
     * 
     * @param string    $ClassName      Object/Entity/Document Class Name
     * @param mixed     $Manager        Entity/Document Manager
     */
    private function loadObjectAnnotation($ClassName, $Manager)
    {
        //====================================================================//
        // Create Annotations reader
        if (!isset($this->reader)) {
            $this->reader = new AnnotationReader();
        } 
        //====================================================================//
        // Search for Splash Objects Annotations
        $Annotation     = $this->reader->getClassAnnotation(new \ReflectionClass($ClassName), 'Splash\Bundle\Annotation\Object');
        //====================================================================//
        // Splash Object Not Found
        if (!$Annotation) {
            return;
        }
        //====================================================================//
        // Splash Object is Disabled
        if ($Annotation->getDisabled()) {
            return;
        }            
        //====================================================================//
        // Store Entity Class Name
        $Annotation->setClass($ClassName);
        //====================================================================//
        // Store Link to Entity Manager
        $Annotation->setManager($Manager);            
        //====================================================================//
        // Store Annotation In Cache
        $this->_objects[$Annotation->getType()] = $Annotation;  
        return;
    }
    
    /**
     *  @abstract   Analyze Object Field Annotations & Store Fields Annotations In Cache 
     * 
     *  @param  string  $ObjectType     Filter on a Specific Type Name
     */
    private function loadFieldsAnnotations($ObjectType)
    {
        //====================================================================//
        // Init Result Array
        $this->_fields[$ObjectType] = [];
        //====================================================================//
        // Load Object Annotations
        $ObjectAnnotation   =   $this->getObjectsAnnotations($ObjectType);
        //====================================================================//
        // Safety Check
        if (!$ObjectAnnotation || !class_exists($ObjectAnnotation->getClass())) {
            return $this->_fields[$ObjectType];
        }
        //====================================================================//
        // Create Reflection Class for Target Object   
        $ReflectionClass = new \ReflectionClass($ObjectAnnotation->getClass());    
        //====================================================================//
        // Create Annotations reader
        $Reader = new AnnotationReader(); 
        //====================================================================//
        // Walk on Object Properties
        foreach ($ReflectionClass->getProperties() as $Property) {
            //====================================================================//
            // Search for Splash Fields Annotations
            $FieldAnnotation = $Reader->getPropertyAnnotation($Property, 'Splash\Bundle\Annotation\Field');
            //====================================================================//
            // Splash Field Found
            if (!$FieldAnnotation) {
                continue;
            }
            //====================================================================//
            // Setup Field
            $FieldAnnotation->setFieldName($Property->getName());
            $this->_fields[$ObjectType][$FieldAnnotation->getId()] = $FieldAnnotation;  
        }    
        return $this->_fields[$ObjectType];
    }    
}



?>
