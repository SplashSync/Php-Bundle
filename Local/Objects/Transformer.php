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
 * @abstract    Local Objects Fields Data Generic Transformer for Splash Bundle
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace Splash\Local\Objects;

use Splash\Client\Splash;
use Splash\Bundle\Annotation\Field;
use Splash\Models\ObjectBase;

class Transformer {
        
    /**
     *  @abstract       Convert Splash Date String to DateTime 
     * 
     *  @param  string  $In             Splash Date String
     * 
     *  @return \DateTime
     */
    protected function importDate($In)
    {
        return new \DateTime($In);
    }
    
    /**
     *  @abstract       Convert DateTime to Splash Date String 
     * 
     *  @param  \DateTime  $In             DateTime Object
     * 
     *  @return string
     */
    protected function exportDate($In)
    {
        return $In ? $In->format(SPL_T_DATECAST) : "";
    }
        
    /**
     *  @abstract       Convert Object to Splash ObjectId String 
     * 
     *  @param  mixed   $In                 Pointed Object
     * 
     *  @return string
     */
    public function exportObjectId($In)
    {
        //====================================================================//
        // Check Pointed Object Exists & Has an Id
        if (!$In || !$In->getId() ) {
            return Null;
        } 
        //====================================================================//
        // Return Object Id
        return $In->getId();
    }

    
    /**
     *  @abstract       Convert Local Data to Splash Format (if changes needed)
     * 
     *  @param  mixed   $Object         Current Local Object
     *  @param  Field   $Annotation     Splash Field Annoatation Object
     *  @param  mixed   $Data           Field Input Splash Formated Data
     * 
     *  @return         mixed       $parameters
     */
    public function import(&$Object, Field $Annotation, $Data)
    {
        //====================================================================//
        // Check if a Transformer is Defined 
        if ( ($Function = $this->hasImportFunction($Annotation)) )
        {
            //====================================================================//
            // Apply Transformation 
            $Data = $this->$Function($Data);
        }
        //====================================================================//
        // Set Data to Object 
        $Object->{ $Annotation->setter() }($Data);
        return $this;
    }
    
    /**
     *  @abstract       Convert Local Data to Splash Format (if changes needed)
     * 
     *  @param  mixed   $Object         Current Local Object
     *  @param  Field   $Annotation     Splash Field Annoatation Object
     * 
     *  @return         mixed       $parameters
     */
    public function export($Object, Field $Annotation)
    {
        //====================================================================//
        // Get Data from Object 
        $Data   =   $Object->{ $Annotation->getter() }();
        //====================================================================//
        // Check if a Transformer is Defined 
        if ( ($Function = $this->hasExportFunction($Annotation)) )
        {
            //====================================================================//
            // Apply Transformation 
            $Data = $this->$Function($Data);
        }
        return $Data;
    }
    
    /*
     * @absract     Check if a Specific Import Transformer is Defined for Field Type
     * @param  Field   $Annotation     Splash Field Annoatation Object
     */
    private function hasImportFunction(Field $Annotation)
    {
        //====================================================================//
        // Check if a Specific Import Function Exists
        if ( method_exists($this, "import" . ucfirst($Annotation->getProperty("type"))) )
        {
            return "import" . ucfirst($Annotation->getProperty("type"));
        }
        return Null;
    }
    
    /*
     * @absract     Check if a Specific Export Transformer is Defined for Field Type
     * @param  Field   $Annotation     Splash Field Annoatation Object
     */
    private function hasExportFunction(Field $Annotation)
    {
        //====================================================================//
        // Detect Object ID Field
        if ( ($ObjectId = ObjectBase::ObjectId_DecodeId($Annotation->getType())) ) {
            return "exportObjectId";
        } 
        //====================================================================//
        // Check if a Specific Import Function Exists
        if ( method_exists($this, "export" . ucfirst($Annotation->getProperty("type"))) )
        {
            return "export" . ucfirst($Annotation->getProperty("type"));
        }
        return Null;
    }
    
    
}
