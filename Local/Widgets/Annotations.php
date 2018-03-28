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
 * @abstract    Local Overiding Widgets Manager for Splash Bundle
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace Splash\Local\Widgets;

use Splash\Core\SplashCore  as Splash;
use Splash\Models\WidgetBase;

use Doctrine\Common\Annotations\AnnotationReader;

class Annotations extends WidgetBase
{
    
    /*
     * @abstract    Static Widgets Class List
     * @var array
     */
    private $_static    = array();
    
    //====================================================================//
    // Annotation Cache Variables
    //====================================================================//
    
    private $_widgets    =   null;
    
    //====================================================================//
    // Class Constructor
    //====================================================================//
        
    /**
     *      @abstract       Class Constructor
     */
    public function __construct($Widgets = array())
    {
        //====================================================================//
        // Store List of Static Widgets
        $this->_static = $Widgets;
    }
    
    /**
     *      @abstract   Analyze Annotations & return Widgets Types List
     */
    public function getWidgetsTypes()
    {
        $WidgetsTypes   =   [];
        //====================================================================//
        // Load Widgets Annotations
        $Annotations = is_null($this->_widgets) ? $this->getAnnotations() : $this->_widgets;
        //====================================================================//
        // Walk on all entities
        foreach ($Annotations as $Annotation) {
            
            //====================================================================//
            // Splash Widget is Disabled
            if ($Annotation->getDisabled()) {
                continue;
            }
            
            $WidgetsTypes[] = $Annotation->getType();
        }
        return $WidgetsTypes;
    }
    
    /**
     * @abstract    Detect Widget Type from Widget Local Class
     *
     * @param   string      $ClassName      Local Widget Class Name
     *
     * @return  string      $WidgetType     Local Widget Splash Type Name or Null if not Mapped
     */
    public function getWidgetType($ClassName)
    {
        //====================================================================//
        // Load Widgets Annotations
        $Annotations = is_null($this->_widgets) ? $this->getAnnotations() : $this->_widgets;
        //====================================================================//
        // Walk on all entities
        foreach ($Annotations as $Annotation) {
            
            //====================================================================//
            // Splash Object is Disabled
            if ($Annotation->getDisabled()) {
                continue;
            }
            //====================================================================//
            // Splash Widget hasn't the Right Class
            if ($ClassName !== $Annotation->getTargetClass()) {
                continue;
            }
            return $Annotation->getType();
        }
        return null;
    }
    
    /**
     *  @abstract   Analyze Annotations & return Widget Description Array
     *
     *  @param  string  $WidgetType         Widget Type Name
     *
     */
    public function getDescription($WidgetType)
    {
        //====================================================================//
        // Load Object Annotations
        $Annotation = $this->getAnnotations($WidgetType);
        if (!$Annotation) {
            return array();
        }
        //====================================================================//
        // Return Widget description Array
        return $Annotation->Description();
    }
        
    /**
     *  @abstract   Analyze Annotations & return Widget Target Class Name
     *
     *  @param  string  $WidgetType         Widget Type Name
     *
     */
    public function getTargetClass($WidgetType)
    {
        //====================================================================//
        // Load Object Annotations
        $Annotation = $this->getAnnotations($WidgetType);
        if (!$Annotation) {
            return null;
        }
        if (!$Annotation->getTargetClass()) {
            return null;
        }
        //====================================================================//
        // Return Widget Target Class
        return $Annotation->getTargetClass();
    }
    
    /**
     *  @abstract   Analyze Annotations & return Widgets Annotations List
     *
     *  @param  string  $WidgetType     Filter on a Specific Type Name
     *
     */
    public function getAnnotations($WidgetType = null)
    {
        //====================================================================//
        // Load Objects Annotations
        if (is_null($this->_widgets)) {
            $this->loadAnnotations();
        }
        
        //====================================================================//
        // If NO Specific Type was requested
        if (is_null($WidgetType)) {
            return $this->_widgets;
        }
        
        //====================================================================//
        // If Specific Type was requested but not found
        if (!is_null($WidgetType) && !isset($this->_widgets[$WidgetType])) {
            return null;
        }
        return $this->_widgets[$WidgetType];
    }

    /**
     *  @abstract   Analyze Annotations
     */
    private function loadAnnotations()
    {
        //====================================================================//
        // Init Result Array
        $this->_widgets = [];
        //====================================================================//
        // Load Static Annotations
        //====================================================================//
        if (!empty($this->_static)) {
            //====================================================================//
            // Walk on all entities
            foreach ($this->_static as $ObjectClass) {
                $this->loadAnnotation($ObjectClass);
            }
        }

        return $this->_widgets;
    }
    
    /**
     * @abstract   Analyze & Load Widgets Class Annotations
     *
     * @param string    $ClassName      Widgets Class Name
     */
    private function loadAnnotation($ClassName)
    {
        //====================================================================//
        // Create Annotations reader
        if (!isset($this->reader)) {
            $this->reader = new AnnotationReader();
        }
        //====================================================================//
        // Search for Splash Objects Annotations
        $Annotation     = $this->reader->getClassAnnotation(new \ReflectionClass($ClassName), 'Splash\Bundle\Annotation\Widget');
        //====================================================================//
        // Splash Widgets Not Found
        if (!$Annotation) {
            return;
        }
        //====================================================================//
        // Splash Widgets is Disabled
        if ($Annotation->getDisabled()) {
            return;
        }
        //====================================================================//
        // Store Entity Class Name
        $Annotation->setClass($ClassName);
        //====================================================================//
        // Store Annotation In Cache
        $this->_widgets[$Annotation->getType()] = $Annotation;
        return;
    }
}
