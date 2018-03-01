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

use Splash\Bundle\Annotation\Field;
use Splash\Models\ObjectBase;
use Splash\Core\SplashCore as Splash;

trait TransformerTrait {

    //====================================================================//
    // OBJECT CREATE UPDATE & DELETE
    //====================================================================//
    
    /**
     *  @abstract       Create a New Object
     * 
     *  @param  mixed   $Manager        Local Object Entity/Document Manager
     *  @param  string  $Target         Local Object Class Name
     * 
     *  @return         mixed
     */
    public function create($Manager, $Target) {            
        //====================================================================//
        // Saftey Check
        if ( !$Target || !class_exists($Target) ) { 
            return False; 
        }
        //====================================================================//
        // Create a New Object
        $Object =   new $Target();
        //====================================================================//
        // Persist New Object        
        $Manager->persist($Object);   
//        $Manager->flush();         
        //====================================================================//
        // Return a New Object
        return  $Object;
    }
    
    /**
     *  @abstract       Update Object Data in Database
     * 
     *  @param  mixed   $Manager        Local Object Entity/Document Manager
     *  @param  string  $Object         Local Object
     * 
     *  @return         mixed
     */
    public function update($Manager, $Object) {            
        //====================================================================//
        // Saftey Check
        if ( !$Object ) { 
            return False; 
        }
        //====================================================================//
        // Save Changes        
        $Manager->flush();         
        //====================================================================//
        // Return Object Id
        return  $Object->getId();
    }    

    /**
     *  @abstract       Create a New Object
     * 
     *  @param  mixed   $Manager        Local Object Entity/Document Manager
     *  @param  string  $Object         Local Object
     * 
     *  @return         mixed
     */
    public function delete($Manager, $Object) {
        //====================================================================//
        // Saftey Check
        if ( !$Object ) { 
            return False; 
        }
        //====================================================================//
        // Delete Object
        $Manager->remove($Object);
        $Manager->flush();        
        return True;
    }
    
    //====================================================================//
    // OBJECT FIELDS IMPORT & EXPORT
    //====================================================================//
    
    /**
     *  @abstract       Import Field Data to Local Object using Field Annotation
     * 
     *  @param  mixed   $Object         Current Local Object
     *  @param  Field   $Annotation     Splash Field Annotation Object
     *  @param  mixed   $Data           Field Input Splash Formated Data
     * 
     *  @return         mixed       $parameters
     */
    public function import(&$Object, Field $Annotation, $Data)
    {
        return $this->importCore($Object, $Annotation->getId(), $Annotation->getType(), $Data);
    }
    
    /**
     *  @abstract       Import Field Data to Local Object 
     * 
     *  @param  mixed   $Object         Current Local Object
     *  @param  string  $Id             Splash Field Id
     *  @param  string  $Type           Splash Field Type
     *  @param  mixed   $Data           Field Input Splash Formated Data
     * 
     *  @return         mixed           Splash Formated Data
     */
    public function importCore($Object, string $Id, string $Type, $Data)
    {
        //====================================================================//
        // Build Setter & Importer Methods Name 
        $Setter     =   "set" . ucfirst($Id);
        //====================================================================//
        // Detect Object ID Field
        if ( ($ObjectType = ObjectBase::ObjectId_DecodeType($Type)) ) {
            $Importer   =   "importObjectId";
        } else {
            $Importer   =   "import" . ucfirst($Type);
        }
        //====================================================================//
        // Detect Object ID Field
        if ( ($ObjectType = ObjectBase::ObjectId_DecodeType($Type)) ) {
            $Data   =   $this->importObjectId($Data,$ObjectType);
        }        
        //====================================================================//
        // Check if a Data Transformer is Defined 
        elseif ( method_exists($this, $Importer) )  {
            $Data   =   $this->$Importer($Data);
        }    
        //====================================================================//
        // Check if a Specific Setter is Defined 
        if ( method_exists($this, $Setter) )  {
            return $this->$Setter($Object,$Data);
        //====================================================================//
        // Fallback to Use Object Getter 
        }
        return $Object->$Setter($Data);
    }  
    
    /**
     *  @abstract       Export Field Data from Local Object using Field Annotation
     * 
     *  @param  mixed   $Object         Current Local Object
     *  @param  Field   $Annotation     Splash Field Annotation Object
     * 
     *  @return         mixed       $parameters
     */
    public function export($Object, Field $Annotation)
    {
        return $this->exportCore($Object, $Annotation->getId(), $Annotation->getType());
    }
    
    /**
     *  @abstract       Export Field Data from Local Object
     * 
     *  @param  mixed   $Object         Current Local Object
     *  @param  string  $Id             Splash Field Id
     *  @param  string  $Type           Splash Field Type
     * 
     *  @return         mixed           Splash Formated Data
     */
    public function exportCore($Object, string $Id, string $Type)
    {
        //====================================================================//
        // Build Getter & Exporter Methods Name 
        $Getter     =   "get" . ucfirst($Id);
        $Exporter   =   "export" . ucfirst($Type);
        //====================================================================//
        // Check if a Specific Getter is Defined 
        if ( method_exists($this, $Getter) )  {
            $Data   =   $this->$Getter($Object);
        //====================================================================//
        // Fallback to Use Object Getter 
        } else {
            $Data   =   $Object->$Getter();
        }   
        //====================================================================//
        // Detect Object ID Field
        if ( ($ObjectType = ObjectBase::ObjectId_DecodeType($Type)) ) {
            return $this->exportObjectId($Data,$ObjectType);
        }
        //====================================================================//
        // Check if a Data Transformer is Defined 
        elseif ( method_exists($this, $Exporter) )  {
            return   $this->$Exporter($Data);
        }        
        return $Data;
    }    
    
    //====================================================================//
    // OBJECT ID FIELDS TRANSFORMERS
    //====================================================================//
    
    /**
     *  @abstract       Convert Local Object to Splash ObjectId String 
     * 
     *  @param  mixed   $In                 Pointed Object
     *  @param  string  $ObjectType         Splash Object Type
     * 
     *  @return string
     */
    public function exportObjectId($In, $ObjectType)
    {
        //====================================================================//
        // Check Pointed Object Exists & Has an Id
        if (!$In || !$ObjectType || !$In->getId() ) {
            return Null;
        } 
        //====================================================================//
        // Return Object Id
        return ObjectBase::ObjectId_Encode($ObjectType,$In->getId());
    }    
    
    /**
     *  @abstract       Convert Splash ObjectId String to Local Object 
     * 
     *  @param  string  $In                Splash ObjectId String 
     *  @param  string  $ObjectType        Splash Object Type
     * 
     *  @return string
     */
    public function importObjectId($In, $ObjectType)
    {
        //====================================================================//
        // Decode Object Id String
        $ObjectId   =   ObjectBase::ObjectId_DecodeId($In);
        //====================================================================//
        // Check Id & Type
        if ( !$ObjectId || !$ObjectType ) {
            return Null;
        } 
        //====================================================================//
        // Load Local Object Manager
        $ObjectManager     =   Splash::Local()->Object($ObjectType);
        $Object            =    $ObjectManager->getRepository()->find($ObjectId);
        //====================================================================//
        // Check Pointed Object Exists & Has an Id
        if ( !$Object || !$Object->getId() ) {
            return Null;
        } 
        //====================================================================//
        // Return Local Object
        return $Object;
    }     
    
    
    //====================================================================//
    // OBJECT LIST INSERT & REMOVE
    //====================================================================//
    
    /**
     *  @abstract       Add Item To an ArrayCollection (List Fields)
     * 
     *  @param  mixed   $Object         Current Local Object
     *  @param  string  $Id             Splash List Id
     * 
     *  @return         mixed           List Item
     */
    public function addItem($Object, string $Id)
    {
        //====================================================================//
        // Build Add Methods Name 
        $Add     =   "add" . ucfirst($Id);
        //====================================================================//
        // Check if a Specific Getter is Defined 
        if ( method_exists($this, $Add) )  {
            return   $this->$Add($Object);
        }   
        //====================================================================//
        // Fallback to Array Mode 
        $List   =   $this->exportCore($Object, $Id, "list");
        $Data   = array();
        $List->add($Data);
        return $Data;
    }

    /**
     *  @abstract       Remove Item From an ArrayCollection (List Fields)
     * 
     *  @param  mixed   $Object         Current Local Object
     *  @param  string  $Id             Splash List Id
     *  @param  mixed   $Item           Local List Item
     * 
     *  @return         mixed           List Item
     */
    public function removeItem($Object, string $Id, $Item)
    {
        //====================================================================//
        // Build Add Methods Name 
        $Remove     =   "remove" . ucfirst($Id);
        //====================================================================//
        // Check if a Specific Getter is Defined 
        if ( method_exists($this, $Remove) )  {
            return   $this->$Remove($Object,$Item);
        }   
        //====================================================================//
        // Fallback to Default Mode 
        $List   =   $this->exportCore($Object, $Id, "list");
        $List->removeItem($Item);
        return True;
    }    
    
}
