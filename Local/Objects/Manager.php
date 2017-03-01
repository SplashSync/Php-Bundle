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

use Doctrine\ORM\EntityManager;

use Splash\Local\Objects\Annotations;

class Manager extends ObjectBase
{
    /**
     * @abstract    Object Type Name
     * @var string 
     */
    private $type   =   null;
    
    /**
     * @abstract    Object Annotation
     * @var \Splash\Bundle\Annotation\Object
     */
    private $annotation   =   null;
    
    /*
     *  Splash Annotations Manager
     * @var \Splash\Local\Objects\Annotations
     */
    private $_am;
    
    /** 
     * @abstract    Entity Data Converter
     * @var \Splash\Local\Objects\Transformer 
     */
    private $transformer = Null;
    
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
        // Link to Splash Annotations Manager
        $this->_am = new Annotations($EntityManager);
        
        if ($ObjectType) {
            //====================================================================//
            // Store Object Type
            $this->type     =   $ObjectType;
            //====================================================================//
            // Load Object Type Annotations
            $this->annotation = $this->_am->getObjectsAnnotations($ObjectType);
            if ( !$this->annotation ) {
                return Splash::Log()->Err("ErrLocalTpl",__CLASS__,__FUNCTION__,"No Definition found for this Object Type (" . $ObjectType . ")");
            }
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
        return $this->_am->getObjectDescription(($this->type));
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
        // Safety Check
        if (!$this->type) {
            return False;
        }         
        //====================================================================//
        // Publish Fields
        return $this->_am->getObjectFields($this->type);
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
        //====================================================================//
        // Init Response Array
        $Response = [];
        //====================================================================//
        // Load Fields Annotations
        $FieldsAnnotations = $this->_am->getObjectFieldsAnnotations($this->type, ["inlist" => true]);
        if ( !$this->annotation || !$FieldsAnnotations) {
            return $Response;
        }
        //====================================================================//
        // Prepare List Parameters
        $Limit = $Offset = Null;
        if ( !empty($params["max"]) && is_numeric($params["max"]) ) {
            $Limit = $params["max"];
        }
        if ( !empty($params["offset"]) && is_numeric($params["offset"]) ) {
            $Offset = $params["offset"];
        }
        $Ordering = array();
        if ( !empty($params["sortfield"]) && !empty($params["sortorder"]) ) {
            $Ordering = array($params["sortfield"] => $params["sortorder"]);
        }
        //====================================================================//
        // Prepare List Filters
        $Search =   array();
//        if ( !empty($filter) ) {
//            $Search["identifier"] = $filter;
//        }
        //====================================================================//
        // Load Objects List
        $Repo   =   $this->getManager()->getRepository($this->annotation->getClass());        
        $RawData = $Repo->findBy($Search, $Ordering, $Limit , $Offset );                  
        //====================================================================//
        // Parse Data on Result Array
        foreach ($RawData as $RawObject) {
            $ObjectData =   ["id"    =>   $RawObject->getId()];
            foreach ($FieldsAnnotations as $FieldId => $FieldAnnotation) {
                $ObjectData[$FieldId] =   $RawObject->{ $FieldAnnotation->getter() }();
            }
            $Response[] = $ObjectData;  
        }
        //====================================================================//
        // Parse Meta Infos on Result Array
        $Response["meta"] =  array(
            "total"   => count($Repo->findBy($Search)),
            "current" => count($RawData)
            );
        return $Response;
    }
    
    /**
     *  @abstract     Reading of requested Object Data
     * 
     *  @param        array   $id               Customers Id.  
     *  @param        array   $list             List of requested fields    
     * 
     *  @return array Object Data
     */
    public function Get($id=NULL,$list=0)
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);  
        //====================================================================//
        // Load Object
        if ( !$this->annotation || !( $this->Object = $this->getManager()->find($this->annotation->getClass(), $id) ) ) {
            return False; 
        }
        //====================================================================//
        // Add Object Id to Data
        $this->Out      =   array( "id" => $this->Object->getId()) ;
        //====================================================================//
        // Read Object Data
        foreach ($list as $FieldId) {
            //====================================================================//
            // Detect List Field
        
            //====================================================================//
            // Load Object Data to Out Buffer
            $this->Out[$FieldId] = $this->getFieldData($FieldId);
        }
        return $this->Out; 
    }
        
    /**
     *   @abstract     Read Requested Object Data and put in Out Buffer
     * 
     *   @param        string  $FieldId          Object Field Id  
     */
    private function getFieldData($FieldId)
    {
        //====================================================================//
        // Load Field Annotations
        if ( !($FieldAnnotation = $this->_am->getObjectFieldAnnotation($this->type, $FieldId)) ) {
            return Splash::Log()->Err("ErrLocalWrongField",__CLASS__,__FUNCTION__, $FieldId);
        }      
        //====================================================================//
        // Detect Object ID Field
        if ( ($ObjectType = ObjectBase::ObjectId_DecodeType($FieldAnnotation->getType())) ) {
            //====================================================================//
            // Load Pointed Object Annotation
            $ObjectAnnotation   =   $this->_am->getObjectsAnnotations($ObjectType);
            //====================================================================//
            // Check Object Annotation was Found
            if (!$ObjectAnnotation) {
                return Null;
            } 
            //====================================================================//
            // Return Splash Object Id
            return ObjectBase::ObjectId_Encode(
                    $ObjectAnnotation->getType(),
                    $this->getTransformer()->export($this->Object, $FieldAnnotation)
                ); 
        }         
        //====================================================================//
        // Read Field Data for Target Object
        return $this->getTransformer()->export($this->Object, $FieldAnnotation);
    }   
    
    
    /**
     *  @abstract     Write or Create requested Object Data
     * 
     *  @param        array   $id               Object Id.  If NULL, Object needs to be created.
     *  @param        array   $list             List of requested fields    
     * 
     *  @return       string  $id               Object Id.  If False, Object wasn't created.    
     */
    public function Set($id=NULL,$list=NULL)
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);
        //====================================================================//
        // Load Object if Id Given
        if ( $id ) {
            //====================================================================//
            // Load Object
            if ( !( $this->Object = $this->getManager()->find($this->annotation->getClass(), $id) ) ) {
                return Splash::Log()->Err("ErrLocalTpl",__CLASS__,__FUNCTION__,"Unable to Load Requested Object. (Type: " . $this->type . " ID : " . $id . ")");
            }
        } else {
            if ( !$this->createObject($list) ) {
                return False;
            }
        }
        //====================================================================//
        // Run through all Received Data
        foreach ( $list as $FieldId => $FieldData) {
            //====================================================================//
            // Write Object Data
            $this->setFieldData($FieldId,$FieldData);
        }
        //====================================================================//
        // Save Changes
        $this->getManager()->flush();        
        return $this->Object->getId();        
    }       

    /**
     *   @abstract     Create a New Object
     * 
     *   @param        array  $FieldsData          Object Inputs Data
     */
    private function createObject($FieldsData) : bool
    {
        //====================================================================//
        // Load list of Required Fields
        $Required   =   $this->_am->getObjectFieldsAnnotations($this->type, ["required" => true]);
        //====================================================================//
        // Check Required Fields are Available
        foreach ( $Required as $FieldAnnotation) {
            //====================================================================//
            // Fields is in Input Array
            if ( !isset($FieldsData[$FieldAnnotation->getId()]) ) {
                return Splash::Log()->Err("ErrLocalFieldMissing",__CLASS__,__FUNCTION__,$FieldAnnotation->getId());
            }
            //====================================================================//
            // Fields is Not Empty
            if ( empty($FieldsData[$FieldAnnotation->getId()]) ) {
                return Splash::Log()->Err("ErrLocalFieldMissing",__CLASS__,__FUNCTION__,$FieldAnnotation->getId());
            }
        }
        //====================================================================//
        // Saftey Check
        if ( !$this->annotation ) { return False; }
        //====================================================================//
        // Create a New Object
        $Classname = $this->annotation->getClass();
        $this->Object   =   new $Classname();
        //====================================================================//
        // Persist New Object        
        $this->getManager()->persist($this->Object);    
        return True;
    }   
    
    /**
     *   @abstract     Read Requested Object Data and put in Out Buffer
     * 
     *   @param        string   $FieldId          Object Field Id  
     *   @param        mixed    $FieldData        Object Field Data  
     */
    private function setFieldData($FieldId,$FieldData) : bool
    {
        //====================================================================//
        // Load Field Annotations
        if ( !($FieldAnnotation = $this->_am->getObjectFieldAnnotation($this->type, $FieldId)) ) {
            return Splash::Log()->Err("ErrLocalWrongField",__CLASS__,__FUNCTION__, $FieldId);
        }
      
        //====================================================================//
        // Detect Object ID Field
        if ( ($ObjectType = ObjectBase::ObjectId_DecodeType($FieldAnnotation->getType())) ) {
            //====================================================================//
            // Load Pointed Object Annotation
            $ObjectAnnotation   =   $this->_am->getObjectsAnnotations($ObjectType);
            //====================================================================//
            // Check Object Annotation was Found
            if (!$ObjectAnnotation) {
                return False;
            } 
            //====================================================================//
            // Decode Object Id
            $ObjectId   =   ObjectBase::ObjectId_DecodeId($FieldData);
            //====================================================================//
            // Load Object
            if ( !( $FieldData = $this->getManager()->find($ObjectAnnotation->getClass(), $ObjectId) ) ) {
                return False; 
            }
        }         
        
        //====================================================================//
        // Detect List Field
        
        //====================================================================//
        // Write Field Data for Target Object
        $this->getTransformer()->import($this->Object, $FieldAnnotation, $FieldData);
        
        return True;
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
        // Safety Check
        if ( !$id ) {
            return False;
        }
echo "delete " . $this->annotation->getClass() . " id " . $id . PHP_EOL;
        //====================================================================//
        // Load Object
        if ( !( $this->Object = $this->getManager()->find($this->annotation->getClass(), $id) ) ) {
            //====================================================================//
            // Object not found (Or Already deleted)
            return True;
        }
        //====================================================================//
        // Delete Object
        $this->getManager()->remove($this->Object);
        $this->getManager()->flush();
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
     * @abstract   Return Doctrine Entity / Document Manager
     */
    public function getManager()
    {
        return $this->annotation->getManager();
    }  
    
    /**
     * @abstract   Return Object Data Transformer
     */
    public function getTransformer()
    {
        
        if( is_null($this->transformer) ) { 
            $this->transformer = Splash::Local()->getTransformer($this->annotation->getTransformerService());            
        }
        
        return $this->transformer;

    }      
    
    /**
     * @abstract   Return Annotation Manager
     */
    public function getAnnotationManager()
    {
        return $this->_am;
    }  
}



?>
