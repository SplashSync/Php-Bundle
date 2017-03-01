<?php

namespace Splash\Bundle\Annotation;

use Splash\Core\SplashCore      as Splash;

use ArrayObject;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Field
{
    //==============================================================================
    //      GENERAL FIELD PROPS
    //==============================================================================
    /** @var string @Required */
    public $id;                                 //  Field Object Unique Identifier
    /** @var string @Required */
    public $type;                               //  Field Fomat Type Name
    /** @var string @Required */
    public $name;                               //  Field Humanized Name (String)
    /** @var boolean */
    public $required        = False;            //  Field is Required to Create a New Object (Bool)
    /** @var string */
    public $desc            = Null;             //  Field Description (String)
    /** @var string */
    public $group           = Null;             //  Field Section/Group (String)
    //==============================================================================
    //      ACCES PROPS
    //==============================================================================
    /** @var boolean */
    public $read            = True;             //  Field is Readable (Bool)
    /** @var boolean */
    public $write           = True;             //  Field is Writable (Bool)
    /** @var boolean */
    public $inlist          = False;            //  Field is Available in Object List Response (Bool)
    //==============================================================================
    //      SCHEMA.ORG IDENTIFICATION
    //==============================================================================
    /** @var string */
    public $itemprop        = Null;             //  Field Unique Schema.Org "Like" Property Name
    /** @var string */
    public $itemtype        = Null;             //  Field Unique Schema.Org Object Url
    private $tag            = Null;
    //==============================================================================
    //      DATA SPECIFIC FORMATS PROPS
    //==============================================================================    
    public $choices        = array();           //  Possible Values used in Editor & Debugger Only  (Array)
    //==============================================================================
    //      DATA LOGGING PROPS
    //==============================================================================
    /** @var boolean */
    public $log             = False;            //  Field is To Log (Bool)
    //==============================================================================
    //      DEBUGGER PROPS
    //==============================================================================
    public $asso            = array();          //  Associated Fields. Fields to Generate When Generating Random value of this field.
    /** @var boolean */
    public $notest          = False;            //  Do No Perform Tests for this Field    
    
    /** @var string */
    private $field;
   
    /*
     * @abstract    get Field Id
     */
    public function getId() 
    {
        return $this->id;
    }
   
    /*
     * @abstract    get Field Type
     */
    public function getType() 
    {
        return $this->type;
    }
    
    /*
     * @abstract    get Field Property
     */
    public function getProperty($Name) 
    {
        if (property_exists($this, $Name)) {
            return $this->$Name;
        }
        return Null;
    }
    
    /*
     * @abstract    get Field Getter Function Name
     */
    public function getter() 
    {
        return "get" . ucwords($this->field);
    }
    
    /*
     * @abstract    get Field Setter Function Name
     */
    public function setter() 
    {
        return "set" . ucwords($this->field);
    }
    
    /*
     * @abstract    Set Entity Field Name
     */
    public function setFieldName($Name) 
    {
        $this->field = $Name;
        return $this;
    }    
    
    /*
     * @abstract    Return Splash Field Definition Array
     */
    public function getDefinition() 
    {
        //==============================================================================
        // Compute tag if metadata given
        if (!empty($this->itemtype) && !empty($this->itemprop)) {
            $this->tag  = md5($this->itemprop . IDSPLIT . $this->itemtype); 
        } 
        //==============================================================================
        // Transfer Name to Description if empty
        if (empty($this->desc)) {
            $this->desc  = $this->name; 
        } 
        
        //==============================================================================
        // Convert Object to Array
        $ArrayDefinition = get_object_vars ( $this );
        //==============================================================================
        // Remove private properties
        unset($ArrayDefinition["field"]);
        
        //==============================================================================
        // Convert Choices Array to Key, Value Array
        $Choices = array();
        foreach ($ArrayDefinition["choices"] as $Value => $Description) {
            $Choices[]  =   array("key" => $Value, "value" => Splash::Trans(trim($Description)));
        }
        $ArrayDefinition["choices"] = $Choices;
        //==============================================================================
        // Return definition Array
        return new ArrayObject($ArrayDefinition,  ArrayObject::ARRAY_AS_PROPS);
    }
        
}
