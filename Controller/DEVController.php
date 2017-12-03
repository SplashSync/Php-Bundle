<?php

namespace Splash\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Splash\Client\Splash;

class DEVController extends Controller
{
    
    /**
     * Execute External SOAP Requests
     */
    public function debugAction($Type = Null, $ObjectId = Null)
    {
        //====================================================================//
        // Boot Local Splash Module
        Splash::Local()->Boot($this->container);
        
        return $this->render('SplashBundle:debug:index.html.twig', $this->debugPrepare($Type, $ObjectId));        
    }        
    
    /**
     * Execute External SOAP Requests
     */
    protected function debugPrepare($Type = Null, $ObjectId = Null)
    {
        //====================================================================//
        // Filter Objects To Debug
        if ($Type) {
            $Objects   =  in_array($Type, Splash::Objects()) ? array($Type) : array();
        } else {
            //====================================================================//
            // List Available Objects
            $Objects   =   Splash::Objects();
        }
        
        //====================================================================//
        // Filter Widgets To Debug
        if ($Type) {
            $Widgets   =  in_array($Type, Splash::Widgets()) ? array($Type) : array();
        } else {
            //====================================================================//
            // List Available Objects
            $Widgets   =   Splash::Widgets();
        }

        //====================================================================//
        // Debug Available Objects
        $Data = array();
        foreach ($Objects as $ObjectType) 
        {
            //====================================================================//
            // Debug Object Main Functions
            $Data[$ObjectType] = array();
            $Data[$ObjectType]["Description"]   =   Splash::Object($ObjectType)->Description();
            $Data[$ObjectType]["Fields"]        =   Splash::Object($ObjectType)->Fields();
            $Data[$ObjectType]["List"]          =   Splash::Object($ObjectType)->ObjectsList(Null,["max" => 10]);
            
            //====================================================================//
            // Select An Object (If not User Selected)
            $index =    rand(0,$Data[$ObjectType]["List"]["meta"]["current"]); 
            if (!$ObjectId && isset($Data[$ObjectType]["List"][$index])) {
                $ObjectId = $Data[$ObjectType]["List"][$index]["id"];
            }
            
            $Data[$ObjectType]["Id"]  = $ObjectId;
            if (!$ObjectId) {
                $Data[$ObjectType]["Data"]  = "Not Found";
                $Data[$ObjectType]["Raw"]   = "Not Found";
                continue;
            }
            
            //====================================================================//
            // Prepare List of Object Fields
            $ObjectFields = [];
            foreach (Splash::Object($ObjectType)->Fields() as $Field) 
            {
                if ($Field["read"]) {
                    $ObjectFields[] = $Field["id"];
                }                        
            }
            //====================================================================//
            // Read Object Data
            $Data[$ObjectType]["Data"]  =   Splash::Object($ObjectType)->Get($ObjectId, $ObjectFields);
            //====================================================================//
            // Read Object Raw Data
            $Data[$ObjectType]["Raw"]   =   Splash::Object($ObjectType)->getRepository()->find($ObjectId);
        }

        //====================================================================//
        // Debug Available Widgets
        $WidgetsData = array();
        foreach ($Widgets as $WidgetType) 
        {
            //====================================================================//
            // Debug Widget Main Functions
            $WidgetsData[$WidgetType] = array();
            $WidgetsData[$WidgetType]["Description"]   =   Splash::Widget($WidgetType)->Description();
            $WidgetsData[$WidgetType]["Options"]       =   Splash::Widget($WidgetType)->Options();
            $WidgetsData[$WidgetType]["Parameters"]    =   Splash::Widget($WidgetType)->Parameters();
            //====================================================================//
            // Read Object Data
            $WidgetsData[$WidgetType]["Data"]          =   Splash::Widget($WidgetType)->Get();
            //====================================================================//
            // Read Object Raw Data
            $WidgetsData[$WidgetType]["Raw"]           =   Splash::Widget($WidgetType);
        }
        
        //====================================================================//
        // Dump Module Log
        $Log    =   Splash::Log()->GetRawLog(True);
        
        return array(
                    "Objects"       =>  $Objects,
                    "Widgets"       =>  $Widgets,
                    "Data"          =>  $Data,
                    "WidgetsData"   =>  $WidgetsData,
                    "Log"           =>  $Log
                );
    }    
    
    
}
