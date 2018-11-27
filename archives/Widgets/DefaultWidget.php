<?php
/*
 * Copyright (C) 2011-2014  Bernard Paquier       <bernard.paquier@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 *
*/
                    
//====================================================================//
// *******************************************************************//
//                     SPLASH FOR SYMFONY                            //
// *******************************************************************//
//                  TEST & DEMONSTRATION WIDGET                       //
// *******************************************************************//
//====================================================================//

namespace   Splash\Local\Widgets;

use Splash\Models\WidgetBase;
use Splash\Core\SplashCore      as Splash;
use Splash\Bundle\Annotation    as SPL;

/**
 * @abstract    Default Widget for Symfony2 Applications
 *
 * @author B. Paquier <contact@splashsync.com>
 *
 * @SPL\Widget( type            =   "Default",
 *              disabled        =   false,
 *              name            =   "Splash Default Widget",
 *              icon            =   "fa fa-exclamation",
 * )
 *
 */
class DefaultWidget extends WidgetBase
{
    
    //====================================================================//
    // Define Standard Options for this Widget
    // Override this array to change default options for your widget
    public static $OPTIONS       = array(
        "Width"     =>      self::SIZE_XL,
    );
    
    //====================================================================//
    // Class Main Functions
    //====================================================================//
    
    /**
     *      @abstract   Return Widget Customs Options
     */
    public function Options()
    {
        return self::$OPTIONS;
    }
    
    /**
     *      @abstract   Return Widget Customs Parameters
     */
    public function Parameters()
    {
        //====================================================================//
        // Reference
        $this->fieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("text_input")
                ->Name("Text Input")
                ->Description("Widget Specific Custom text Input");
        
        //====================================================================//
        // Reference
        $this->fieldsFactory()->Create(SPL_T_INT)
                ->Identifier("integer_input")
                ->Name("Numeric Input")
                ->Description("Widget Specific Custom Numeric Input");
        
        //====================================================================//
        // Publish Fields
        return $this->fieldsFactory()->Publish();
    }
    
    /**
     *  @abstract     Return requested Customer Data
     *
     *  @param        array $params Search parameters for result List.
     *                              $params["start"]      Maximum
     *                              Number of results $params["end"]
     *                              List Start Offset
     *                              $params["groupby"]    Field name
     *                              for sort list (Available fields
     *                              listed below)Number of results $params["end"]

     */
    public function Get($params = null)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        
        //====================================================================//
        // Setup Widget Core Informations
        //====================================================================//

        $this->setTitle($this->getName());
        $this->setIcon($this->getIcon());
        
        //====================================================================//
        // Build Intro Text Block
        //====================================================================//
        $this->buildIntroBlock();
          
        //====================================================================//
        // Build Inputs Block
        //====================================================================//
        $this->buildParametersBlock($params);
        
        //====================================================================//
        // Build Inputs Block
        //====================================================================//
        $this->buildNotificationsBlock();

        //====================================================================//
        // Set Blocks to Widget
        $this->setBlocks($this->BlocksFactory()->Render());

        //====================================================================//
        // Publish Widget
        return $this->Render();
    }
        

    //====================================================================//
    // Blocks Generation Functions
    //====================================================================//

    /**
    *   @abstract     Block Building - Text Intro
    */
    private function buildIntroBlock()
    {
        //====================================================================//
        // Into Text Block
        $this->BlocksFactory()->addTextBlock("This is a Demo Text Block!!"."You can repeat me as much as you want!");
    }
  
    /**
    *   @abstract     Block Building - Inputs Parameters
    */
    private function buildParametersBlock($Inputs = array())
    {

        //====================================================================//
        // verify Inputs
        if (!is_array($Inputs) && !is_a($Inputs, "ArrayObject")) {
            $this->BlocksFactory()->addNotificationsBlock(array("warning" => "Inputs is not an Array! Is ".get_class($Inputs)));
        }
        
        //====================================================================//
        // Parameters Table Block
        $TableContents = array();
        $TableContents[]    =   array("Received ".count($Inputs)." inputs parameters", "Value");
        foreach ($Inputs as $key => $value) {
            $TableContents[]    =   array($key, $value);
        }
        
        $this->BlocksFactory()->addTableBlock($TableContents, array("Width" => self::SIZE_M));
    }
    
    /**
    *   @abstract     Block Building - Notifications Parameters
    */
    private function buildNotificationsBlock()
    {

        //====================================================================//
        // Notifications Block
        
        $Notifications = array(
            "error" =>  "This is a Sample Error Notification",
            "warning" =>  "This is a Sample Warning Notification",
            "success" =>  "This is a Sample Success Notification",
            "info" =>  "This is a Sample Infomation Notification",
        );
        
        
        $this->BlocksFactory()->addNotificationsBlock($Notifications, array("Width" => self::SIZE_M));
    }
    
    //====================================================================//
    // Class Tooling Functions
    //====================================================================//
}
