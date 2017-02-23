<?php

namespace Splash\Bundle\Annotation;

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
    
    
//    /*
//     * @abstract    verify Field Definition is Conform
//     */
//    public function Validate() 
//    {
//        
//        return get_object_vars ( $this );
//    }
    
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
        
        return get_object_vars ( $this );
    }
    
    
}
