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
 * @abstract    Local Overriding Objects Manager for Splash Bundle
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace Splash\Local\Objects;

use Splash\Core\SplashCore  as Splash;
use Splash\Models\ObjectBase;

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
    
    /**
     * @abstract    Target Object Class
     * @var string
     */
    private $target   =   null;
        
    /**
     * @abstract    Object Repository
     * @var string
     */
    private $repository   =   null;
    
    /*
     *  Splash Annotations Manager
     * @var \Splash\Local\Objects\Annotations
     */
    private $_am;
    
    /**
     * @abstract    Entity Data Converter
     * @var \Splash\Local\Objects\Transformer
     */
    private $transformer = null;
    
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
    protected static $NAME            =  "Fake Object";
    
    /**
     *  Object Description (Translated by Module)
     */
    protected static $DESCRIPTION     =  "Splash NodesFakerBunlde Generic Object";
    
    /**
     *  Object Icon (FontAwesome or Glyph ico tag)
     */
    protected static $ICO             =  "fa fa-magic";
    
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
    public function __construct(Annotations $AnnotationsManager, $Container, $ObjectType = null)
    {
        //====================================================================//
        // Link to Splash Annotations Manager
        $this->_am = $AnnotationsManager;
        //====================================================================//
        // Safety Check
        if (!$ObjectType) {
            return;
        }
        //====================================================================//
        // Store Object Type
        $this->type     =   $ObjectType;
        //====================================================================//
        // Load Object Type Annotations
        $this->annotation = $this->_am->getObjectsAnnotations($ObjectType);
        if (!$this->annotation) {
            return Splash::log()->err("ErrLocalTpl", __CLASS__, __FUNCTION__, "No Definition found for this Object Type (" . $ObjectType . ")");
        }
        $this->target = $this->annotation->getTargetClass();
        //====================================================================//
        // Init Repository
        $RepositoryService = $this->annotation->getRepositoryService();
        if ($RepositoryService) {
            $this->repository     =   $Container->get($RepositoryService);
        } else {
            $this->repository     =   $this->annotation->getManager()->getRepository($this->target);
        }
        
        return true;
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
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        //====================================================================//
        // Safety Check
        if (!$this->type) {
            return false;
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
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        //====================================================================//
        // Safety Check
        if (!$this->type) {
            return false;
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
    public function ObjectsList($filter=null, $params=null)
    {
        Splash::log()->deb("MsgLocalFuncTrace", __CLASS__, __FUNCTION__);
        //dump($this->_am->getObjectFieldsAnnotations($this->type, ["inlist" => true]));
        //====================================================================//
        // Init Response Array
        $Response = [];
        //====================================================================//
        // Load Fields Annotations
        $FieldsAnnotations = $this->_am->getObjectFieldsAnnotations($this->type, ["inlist" => true]);
        if (!$this->target || !$FieldsAnnotations) {
            return $Response;
        }
        //====================================================================//
        // Prepare List Parameters
        $Limit = $Offset = null;
        if (!empty($params["max"]) && is_numeric($params["max"])) {
            $Limit = $params["max"];
        }
        if (!empty($params["offset"]) && is_numeric($params["offset"])) {
            $Offset = $params["offset"];
        }
        $Ordering = array();
        if (!empty($params["sortfield"]) && !empty($params["sortorder"])) {
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
        $RawData = $this->getRepository()->findBy($Search, $Ordering, $Limit, $Offset);
        //====================================================================//
        // Parse Data on Result Array
        foreach ($RawData as $RawObject) {
            $ObjectData =   ["id"    =>   $RawObject->getId()];
            foreach ($FieldsAnnotations as $FieldId => $FieldAnnotation) {
                $ObjectData[$FieldId] =   $this->getTransformer()->export($RawObject, $FieldAnnotation);
            }
            $Response[] = $ObjectData;
        }
        //====================================================================//
        // Parse Meta Infos on Result Array
        $Response["meta"] =  array(
            "total"   => count($this->getRepository()->findBy($Search)),
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
    public function Get($id=null, $list=0)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        //====================================================================//
        // Load Object
        if (!($this->Object = $this->getRepository()->find($id))) {
            return false;
        }
        //====================================================================//
        // Add Object Id to Data
        $this->Out      =   array( "id" => $this->Object->getId()) ;
        //====================================================================//
        // Read Object Data
        foreach ($list as $FieldId) {
            //====================================================================//
            // Export List Field if Detected
            if ($this->getFieldListData($FieldId)) {
                continue;
            }
            //====================================================================//
            // Simple Loading of Object Data to Out Buffer
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
        if (!($FieldAnnotation = $this->_am->getObjectFieldAnnotation($this->type, $FieldId))) {
            return Splash::log()->err("ErrLocalWrongField", __CLASS__, __FUNCTION__, $FieldId);
        }
        //====================================================================//
        // Read Field Data for Target Object
        return $this->getTransformer()->export($this->Object, $FieldAnnotation);
    }
    
    /**
     *   @abstract     Read Requested Object List Fields Data and put in Out Buffer
     *
     *   @param        string  $FieldId          Object Field Id
     */
    private function getFieldListData($FieldId)
    {
        //====================================================================//
        // Check Field is Not a List Field
        if (!($ListName = self::ListField_DecodeListName($FieldId))) {
            return false;
        }
        //====================================================================//
        // Load Field Annotations
        if (!($FieldAnnotation = $this->_am->getObjectFieldAnnotation($this->type, $FieldId))) {
            return Splash::log()->err("ErrLocalWrongField", __CLASS__, __FUNCTION__, $FieldId);
        }
        //====================================================================//
        // Init List Field Outputs
        self::List_InitOutput($ListName, $FieldId);
        //====================================================================//
        // Load List Data from Object
        $ListData = $this->getTransformer()->exportCore($this->Object, $ListName, "list");
        //====================================================================//
        // Decode Items Data Id & Types
        $ItemId     =   self::ListField_DecodeFieldName($FieldId);
        $ItemType   =   self::ListField_DecodeFieldName($FieldAnnotation->getType());
        if (empty($ItemId) || empty($ItemType)) {
            return Splash::log()->err("ErrLocalTpl", __CLASS__, __FUNCTION__, "Invalid List Field Definition. (Check Field: " . $FieldId . ")");
        }
        //====================================================================//
        // Walk on List Items
        foreach ($ListData as $Key => $Item) {
            //====================================================================//
            // Retrieve Field Item Data
            $ItemData   =   $this->getTransformer()->exportCore($Item, $ItemId, $ItemType);
            //====================================================================//
            // Insert Field in List
            self::List_Insert($ListName, $FieldId, $Key, $ItemData);
        }
        return true;
    }
    
    /**
     *  @abstract     Write or Create requested Object Data
     *
     *  @param        array   $id               Object Id.  If NULL, Object needs to be created.
     *  @param        array   $list             List of requested fields
     *
     *  @return       string  $id               Object Id.  If False, Object wasn't created.
     */
    public function Set($id=null, $list=null)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        //====================================================================//
        // Load Object if Id Given
        if ($id) {
            //====================================================================//
            // Load Object
            if (!($this->Object = $this->getRepository()->find($id))) {
                return Splash::log()->err("ErrLocalTpl", __CLASS__, __FUNCTION__, "Unable to Load Requested Object. (Type: " . $this->type . " ID : " . $id . ")");
            }
        } else {
            if (!$this->createObject($list)) {
                return false;
            }
        }
        //====================================================================//
        // Run through all Received Data
        foreach ($list as $FieldId => $FieldData) {
            //====================================================================//
            // Import List Field if Detected
            if ($this->setFieldListData($FieldId, $FieldData)) {
                continue;
            }
            //====================================================================//
            // Write Object Data
            $this->setFieldData($FieldId, $FieldData);
        }
        //====================================================================//
        // Save Changes
        $Response = $this->getTransformer()->update($this->getManager(), $this->Object);
        //====================================================================//
        // Clear Entity Repository (For Cache)
        $this->getRepository()->clear();
        return  $Response;
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
        foreach ($Required as $FieldAnnotation) {
            //====================================================================//
            // Fields is in Input Array
            if (!isset($FieldsData[$FieldAnnotation->getId()])) {
                return Splash::log()->err("ErrLocalFieldMissing", __CLASS__, __FUNCTION__, $FieldAnnotation->getId());
            }
            //====================================================================//
            // Fields is Not Empty
            if (empty($FieldsData[$FieldAnnotation->getId()])) {
                return Splash::log()->err("ErrLocalFieldMissing", __CLASS__, __FUNCTION__, $FieldAnnotation->getId());
            }
        }
        //====================================================================//
        // Create a New Object
        $this->Object   =   $this->getTransformer()->create($this->getManager(), $this->target);
        return $this->Object ? true : false;
    }
    
    /**
     *  @abstract     Read Requested Object Data and put in Out Buffer
     *
     *  @param      string  $FieldId        Object Field Id
     *  @param      mixed   $FieldData      Object Field Data
     *
     *  @return     bool
     */
    private function setFieldData($FieldId, $FieldData) : bool
    {
        //====================================================================//
        // Load Field Annotations
        if (!($FieldAnnotation = $this->_am->getObjectFieldAnnotation($this->type, $FieldId))) {
            return Splash::log()->err("ErrLocalWrongField", __CLASS__, __FUNCTION__, $FieldId);
        }
        //====================================================================//
        // Write Field Data for Target Object
        $this->getTransformer()->import($this->Object, $FieldAnnotation, $FieldData);
        return true;
    }
    
    /**
     *  @abstract       Read Requested Object List Fields Data and put in Out Buffer
     *
     *  @param      string  $FieldId        Object Field Id
     *
     *  @return     bool
     */
    private function setFieldListData($FieldId, $FieldData)
    {
        //====================================================================//
        // Load list of Object Available Lists
        $Lists  =   $this->_am->getObjectListsNamesArray($this->type);
        //====================================================================//
        // Check FieldId is a List
        if (empty($Lists) || !in_array($FieldId, $Lists)) {
            return false;
        }
        //====================================================================//
        // Load List Data from Object
        $ListData = $this->getTransformer()->exportCore($this->Object, $FieldId, "list");
        //====================================================================//
        // Load First List Item
        $CurrentItem =  $ListData->first();
        //====================================================================//
        // Walk on List Items
        foreach ($FieldData as $Item) {
            //====================================================================//
            // If Item Doesn't Exists => Add Item
            if (!$CurrentItem) {
                $CurrentItem   =   $this->getTransformer()->addItem($this->Object, $FieldId);
            }
            //====================================================================//
            // Walk on List Item Fields
            foreach ($Item as $Id => $Value) {
                $ListFieldId = $Id . LISTSPLIT . $FieldId;
                //====================================================================//
                // Load Field Annotations
                if (!($FieldAnnotation = $this->_am->getObjectFieldAnnotation($this->type, $ListFieldId))) {
                    Splash::log()->err("ErrLocalWrongField", __CLASS__, __FUNCTION__, $ListFieldId);
                    continue;
                }
                //====================================================================//
                // Decode Field Type
                $FieldType  =   self::ListField_DecodeFieldName($FieldAnnotation->getType());
                //====================================================================//
                // Update Item Field Data
                $this->getTransformer()->importCore($CurrentItem, $Id, $FieldType, $Value);
            }
            //====================================================================//
            // Load Next List Item
            $CurrentItem =  $ListData->next();
        }
        //====================================================================//
        // Remove on List Remaining Items
        while ($CurrentItem) {
            $this->getTransformer()->removeItem($this->Object, $FieldId, $CurrentItem);
            //====================================================================//
            // Load Next List Item
            $CurrentItem =  $ListData->next();
        }
        return true;
    }
    
    /**
    *   @abstract   Delete requested Object
    *   @param      int         $id             Object Id.  If NULL, Object needs to be created.
    *   @return     int                         0 if KO, >0 if OK
    */
    public function Delete($id=null)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        //====================================================================//
        // Safety Check
        if (!$id) {
            return false;
        }
        //====================================================================//
        // Load Object
        if (!($this->Object = $this->getRepository()->find($id))) {
            //====================================================================//
            // Object not found (Or Already deleted)
            return true;
        }
        //====================================================================//
        // Delete Object
        $Response = $this->getTransformer()->delete($this->getManager(), $this->Object);
        //====================================================================//
        // Clear Entity Repository (For Cache)
        $this->getRepository()->clear();
        return  $Response;
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
        if (!is_array($ListData) && !is_a($ListData, "ArrayObject")) {
            return;
        }
        
        //====================================================================//
        // Create List Array If Needed
        if (!array_key_exists($ListName, $this->In)) {
            $this->In[$ListName] = array();
        }
            
        $Index = 0;
        //====================================================================//
        // Import List Items
        foreach ($ListData as $ItemData) {
            
            //====================================================================//
            // Create Line Array If Needed
            if (!array_key_exists($Index, $this->In[$ListName])) {
                $this->In[$ListName][$Index] = array();
            }
            
            //====================================================================//
            // Import Items Field Data
            foreach ($ItemData as $FieldId => $FieldData) {

                //====================================================================//
                // Verify Field Id is Set for This Object
                if (!in_array($FieldId . LISTSPLIT . $ListName, $FieldList)) {
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
     * @abstract   Return Doctrine Entity / Document Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }
    
    /**
     * @abstract   Return Object Data Transformer
     */
    public function getTransformer()
    {
        if (is_null($this->transformer)) {
            $this->transformer = Splash::local()->getTransformer($this->annotation->getTransformerService());
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
