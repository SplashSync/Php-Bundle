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

namespace Splash\Local;

use Splash\Core\SplashCore  as Splash;
use Splash\Models\ObjectBase;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;

class ObjectsManager extends ObjectBase
{
    
    /**
     *  @var string Object Type Name
     */
    private $type   =   null;
    
    /*
     *  Doctrine Entity Manager
     * @var \Doctrine\ORM\EntityManager
     */
    private $_em;
    
    /**
     *  @var \Nodes\FakerBundle\Entity\FakeNode
     */
    private $fake   = Null;
    
    //====================================================================//
    // Object Definition Parameters	
    //====================================================================//
    
    /**
     *  Object Disable Flag. Uncomment thius line to Override this flag and disable Object.
     */
//    protected static    $DISABLED        =  True;
    
    /**
     *  Object Name (Translated by Module)
     */
    protected static    $NAME            =  "Fake Object";
    
    /**
     *  Object Description (Translated by Module) 
     */
    protected static    $DESCRIPTION     =  "Splash NodesFakerBunlde Generic Object";    
    
    /**
     *  Object Icon (FontAwesome or Glyph ico tag) 
     */
    protected static    $ICO             =  "fa fa-magic";
    
    //====================================================================//
    // General Class Variables	
    //====================================================================//

    //====================================================================//
    // Class Constructor
    //====================================================================//
        
    /**
     *      @abstract       Class Constructor (Used only if localy necessary)
     *      @return         int                     0 if KO, >0 if OK
     */
    function __construct(EntityManager $EntityManager, $ObjectType = Null) 
    {
        //====================================================================//
        // Link to Doctrine Entity Manager Services
        $this->_em = $EntityManager;

        if ($ObjectType) {
            //====================================================================//
            // Store Object Type
            $this->type     =   $ObjectType;
        }
        
        return True;
    }    
    
    //====================================================================//
    // Class Main Functions
    //====================================================================//
    
    /**
     *  @abstract   Override Get Description Array for requested Object Type
     * 
     *  @return     array
     */    
    public function Description()
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);  
        //====================================================================//
        // Safety Check
        if (!$this->type) {
            return False;
        } 
        //====================================================================//
        // Build & Return Object Description Array
        return $this->getObjectsAnnotations($this->type)->getObjectDescription();
    }      
        
    /**
    *   @abstract     Return List Of available data for Customer
    *   @return       array   $data             List of all customers available data
    *                                           All data must match with OSWS Data Types
    *                                           Use OsWs_Data::Define to create data instances
    */
    public function Fields()
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);  
        //====================================================================//
        // Init response
        $Response = [];
        //====================================================================//
        // Load Object Type Annotations
        $Annotations = $this->getObjectsAnnotations($this->type);
        if (!$Annotations) {
            return $Response;
        }
        //====================================================================//
        // Load Fields Annotations
        foreach ($this->getFieldsAnnotations($Annotations->getClass()) as $Annotation) {
            $Response[] = $Annotation->getDefinition();
        }
        
        //====================================================================//
        // Publish Fields
        return $Response;
    }
    
    /**
    *   @abstract     Return List Of Customer with required filters
     * 
    *   @param        string  $filter                   Filters/Search String for Contact List. 
    *   @param        array   $params                   Search parameters for result List. 
    *                         $params["max"]            Maximum Number of results 
    *                         $params["offset"]         List Start Offset 
    *                         $params["sortfield"]      Field name for sort list (Available fields listed below)    
    *                         $params["sortorder"]      List Order Constraign (Default = ASC)    
     * 
    *   @return       array   $data                     List of all customers main data
    *                         $data["meta"]["total"]     ==> Total Number of results
    *                         $data["meta"]["current"]   ==> Total Number of results
    */
    public function ObjectsList($filter=NULL,$params=NULL)
    {
        Splash::Log()->Deb("MsgLocalFuncTrace",__CLASS__,__FUNCTION__);             

        $Response = [];
        $Repo   =   $this->_em->getRepository('NodesFakerBundle:FakeObject');        
        
        //====================================================================//
        // Prepare List Filters List
        $Search     =   array(
            "node"      => $this->fake,
            "type"      => $this->type,
                );
        if ( !empty($filter) ) {
            $Search["identifier"] = $filter;
        }
        //====================================================================//
        // Load Objects List
        $Data = $Repo->findBy($Search, array(), $params["max"] , $params["offset"] );
            
        //====================================================================//
        // Load Object Fields
        $Fields =   $this->fake->getObjectFields($this->type);

        //====================================================================//
        // Parse Data on Result Array
        foreach ($Data as $Object) {
            
            $ObjectData =   array(
                "id"    =>   $Object->getIdentifier()
                    );
            
            foreach ($Fields as $Field) {
                if ( $Field["inlist"] ) {
                    $ObjectData[$Field["id"]] =   $Object->getData($Field["id"]);
                }
            }
            
            $Response[] = $ObjectData;  
        }
            
        //====================================================================//
        // Parse Meta Infos on Result Array
        $Response["meta"] =  array(
            "total"   => $Repo->getTypeCount($this->fake,$this->type, $filter), 
            "current" => count($Data)
            );
        
        //====================================================================//
        // Return result
        return $Response;
    }
    
    /**
    *   @abstract     Return requested Customer Data
    *   @param        array   $id               Customers Id.  
    *   @param        array   $list             List of requested fields    
    */
    public function Get($id=NULL,$list=0)
    {
        global $kernel;
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);  
        
        //====================================================================//
        // Format List
        if (is_a($list, "ArrayObject")) {
            $list = $list->getArrayCopy();
        }
        
        //====================================================================//
        // Load Object
        $FakeObject = $this->fake->getObject($this->type, $id);
        if ( !$FakeObject ) {
            return $this->Out; 
        }
        
        //====================================================================//
        // Link to Fake Node Entity
        $Formater = $kernel->getContainer()
                ->get("OpenObject.Formater.Service");
        
        //====================================================================//
        // Load Requested Object Data
        $this->Out  =   $Formater->filterData($FakeObject->getData(), $list);
        
        //====================================================================//
        // Add Object Id to Data
        $this->Out["id"]    =   $id;
//        foreach ($FakeObject->getData() as $FieldId => $FieldData) {
//            //====================================================================//
//            // If Field is In Requested List
//            if ( in_array($FieldId, $list) ) {
//                //====================================================================//
//                // Insert Field Data in Response
//                $this->Out[$FieldId]    =    $FieldData;
//            }
//        }
        return $this->Out; 
    }
        
    /**
    *   @abstract     Write or Create requested Customer Data
    *   @param        array   $id               Customers Id.  If NULL, Customer needs t be created.
    *   @param        array   $list             List of requested fields    
    *   @return       string  $id               Customers Id.  If NULL, Customer wasn't created.    
    */
    public function Set($id=NULL,$list=NULL)
    {
        global $kernel;        
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);
        
        //====================================================================//
        // Create Object if Needed
        if ( !$id ) {
            $FakeObject     =   $this->fake->createObject($this->type);
            if ($FakeObject) {
                $this->_em->persist($FakeObject);
                $this->_em->flush();
            }
            $id         =   $FakeObject->getIdentifier();
            $this->In   =   array();
        } else {
            $this->In   = $this->fake->getObjectData($this->type, $id);
        }
        
        //====================================================================//
        // Geneerate reduced Fields List
        $FieldList = $kernel->getContainer()
                ->get("OpenObject.Formater.Service")
                ->reduceFieldList($this->fake->getObjectFields($this->type));
        
        //====================================================================//
        // Run through all Received Data
        foreach ( $list as $FieldId => $FieldData) {
            
            //====================================================================//
            // Detect Simple Field Id
            if (in_array($FieldId, $FieldList)) {
                //====================================================================//
                // Update Requested Object Simple Data
                $this->In[$FieldId]    =    $FieldData;
                
                continue;
            }            

            //====================================================================//
            // Manage List Data
            //====================================================================//
            
            $this->SetList($FieldId, $FieldData, $FieldList);
            
        }
        
        //====================================================================//
        // Save Changes
        $this->fake->setObjectData($this->type, $id, $this->In);
        $this->_em->flush();        
        
        return $id;        
    }       

    /**
    *   @abstract   Delete requested Object
    *   @param      int         $id             Object Id.  If NULL, Object needs to be created.
    *   @return     int                         0 if KO, >0 if OK 
    */    
    public function Delete($id=NULL)
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);  
        //====================================================================//
        // Load Object
        $FakeObject   = $this->fake->getObject($this->type, $id);
        if ( !$FakeObject ) {
            return True;
        }
        //====================================================================//
        // Remove Object from Node
        $this->fake->removeObject($FakeObject);
        //====================================================================//
        // Delete Object
        $this->_em->remove($FakeObject);
        $this->_em->flush();
        
        return True;
    }       

    //====================================================================//
    // Class Tooling Functions
    //====================================================================//

    /**
     *      @abstract   Return name of this Object Class
     */
    public function getName()
    {
        return "Fake " . ucfirst($this->type);
    }

    public function SetList($ListName, $ListData, $FieldList)
    {
        //====================================================================//
        // Check List Data is Array
        if ( !is_array($ListData) && !is_a($ListData, "ArrayObject")){
            return;
        }        
        
        //====================================================================//
        // Create List Array If Needed
        if (!array_key_exists($ListName,$this->In)) {
            $this->In[$ListName] = array();
        }
            
        $Index = 0;
        //====================================================================//
        // Import List Items
        foreach ($ListData as $ItemData) {
            
            //====================================================================//
            // Create Line Array If Needed
            if (!array_key_exists($Index,$this->In[$ListName])) {
                $this->In[$ListName][$Index] = array();
            }    
            
            //====================================================================//
            // Import Items Field Data
            foreach ($ItemData as $FieldId => $FieldData) {

                //====================================================================//
                // Verify Field Id is Set for This Object
                if ( !in_array($FieldId . LISTSPLIT . $ListName, $FieldList)) {
                    continue;
                }   
                
                //====================================================================//
                // Store Field Data in Array
                $this->In[$ListName][$Index][$FieldId] = $FieldData;                

            }
            
            $Index++;

        }
        
    }          
        
    /**
     *  @abstract   Analyze Annotations & return Objects Annotations List 
     *  @param  string  $ObjectType     Filter on a Specific Type Name
     */
    private function getObjectsAnnotations($ObjectType = Null)
    {
        //====================================================================//
        // Init Result Array
        $ObjectsTypes = [];
        //====================================================================//
        // Load Doctrine Metadata
        $MetaData = $this->_em->getMetadataFactory()->getAllMetadata();
        //====================================================================//
        // Create Annotations reader
        $Reader = new AnnotationReader();
        //====================================================================//
        // Walk on all entities
        foreach ($MetaData as $EntityData) {
            //====================================================================//
            // Search for Splash Objects Annotations
            $ClassAnnotation = $Reader->getClassAnnotation(new \ReflectionClass($EntityData->getName()), 'Splash\Bundle\Annotation\Object');
            //====================================================================//
            // Splash Object Found
            if (!$ClassAnnotation) {
                continue;
            }
            //====================================================================//
            // Store Entity Class Name
            $ClassAnnotation->setClass($EntityData->getName());
            //====================================================================//
            // If No Specific Type was requested
            if ( is_null($ObjectType) && $ClassAnnotation->getType()) {
                $ObjectsTypes[] = $ClassAnnotation;  
                continue;
            }
            //====================================================================//
            // If Type matched
            if ( $ClassAnnotation->getType() === $ObjectType ) {
                return $ClassAnnotation;
            }
        }    
        //====================================================================//
        // If Specific Type was requested but not found
        if ( !is_null($ObjectType) ) {
            return Null;
        }
        return $ObjectsTypes;
        
    }
    
    /**
     *  @abstract   Analyze Annotations & return Objects Fields Annotations List 
     *  @param  string  $ClassName     Splash Object target Class Name
     */
    private function getFieldsAnnotations($ClassName)
    {
        //====================================================================//
        // Init Result Array
        $FieldsTypes = [];
        //====================================================================//
        // Safety Check
        if (!class_exists($ClassName)) {
            return $FieldsTypes;
        }
        
        //====================================================================//
        // Create Reflection Class for Target Object   
        $ReflectionClass = new \ReflectionClass($ClassName);
        //====================================================================//
        // Create Annotations reader
        $Reader = new AnnotationReader();        
        //====================================================================//
        // Walk on Object Properties
        foreach ($ReflectionClass->getProperties() as $Property) {
            //====================================================================//
            // Search for Splash Fields Annotations
            $FieldsAnnotation = $Reader->getPropertyAnnotation($Property, 'Splash\Bundle\Annotation\Field');
            //====================================================================//
            // Splash Field Found
            if (!$FieldsAnnotation) {
                continue;
            }
            $FieldsTypes[] = $FieldsAnnotation;  
        }    
        return $FieldsTypes;
    }
    
    /**
     *      @abstract   Analyze Annotations & return Objects Types List 
     */
    public function getObjectsTypes()
    {
        //====================================================================//
        // Load Objects Annotations
        $Annotations = $this->getObjectsAnnotations();

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
    
}



?>
