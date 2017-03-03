<?php

namespace Splash\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Splash\Server\SplashServer;
use Splash\Client\Splash;



use Doctrine\Common\Annotations\AnnotationReader;
use Splash\Bundle\Conversion\SplashFieldConverter;
//use Acme\DataBundle\Entity\Person;


class DEVController extends Controller
{
    
    /**
     * Execute External SOAP Requests
     */
    public function debugAction($ObjectType = Null, $ObjectId = Null)
    {
        //====================================================================//
        // Boot Local Splash Module
        Splash::Local()->Boot($this->container);
        
        //====================================================================//
        // Filter Objects To Debug
        if ($ObjectType) {
            $Objects   =   array($ObjectType);
        } else {
            //====================================================================//
            // List Available Objects
            $Objects   =   Splash::Objects();
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
                    
//            $Product = Splash::Object($ObjectType)->getRepository()->find($ObjectId)->getProduct()->getImages()->toArray();
//            
//            $Product->setCurrentLocale("fr_Fr"); // Get product with id 4, returns null if not found.
//            $Object = $Product->getName(); // Get product with id 4, returns null if not found.
//            $Object = Splash::Object($ObjectType)->getRepository()->find($ObjectId)->getChannelPricings()->first(); // Get product with id 4, returns null if not found.
//            $Object = Splash::Object($ObjectType)->getRepository()->find($ObjectId)->getVariants()->toArray(); // Get product with id 4, returns null if not found.
//            $Object = Splash::Object($ObjectType)->getRepository()->find($ObjectId)->getAttributes()->toArray(); // Get product with id 4, returns null if not found.
            //
            //
//            $Object = $th$Object = $this->get("doctrine")->getManager()->find("Sylius\Component\Product\Model\Product",$ObjectId, "en_US");is->get("doctrine")->getManager()->find("Sylius\Component\Product\Model\Product",$ObjectId, "en_US");
//            $Object = $this->get("doctrine")->getManager()->getRepository("Sylius\Component\Product\Model\Product")->findByName($ObjectId, "en_US");
//            $Object = $this->get("doctrine")->getManager()->find("Sylius\Component\Product\Model\ProductTranslation",$ObjectId);
//            $Object->setLocale("en");
//            dump($Object->getName());
//            $Data[$ObjectType]["Raw"]   =   $Object;
//            $Data[$ObjectType]["Raw"]   =   $Product;
            
//            $Data[$ObjectType]["Raw"]   =   $this->get("doctrine")->getManager()->find("Sylius\Component\Product\Model\Product",$ObjectId)->getAttributes();
//            $Data[$ObjectType]["Raw"]   =   $this->get("doctrine")->getManager()->find("Sylius\Component\Product\Model\Product",$ObjectId)->getAttributes();
//            $Data[$ObjectType]["Raw"]   =   $this->get("doctrine")->getManager()->find("Sylius\Component\Product\Model\Product",$ObjectId)->getAttributes();
//            $Data[$ObjectType]["Raw"]   =   $this->get("doctrine")->getManager()->find("Sylius\Component\Product\Model\Product",$ObjectId)->getDescription();
            
        }

        //====================================================================//
        // Dump Module Log
        $Log    =   Splash::Log()->GetRawLog(True);
        
        return $this->render('SplashBundle::dev.html.twig',array(
                    "Objects"   =>  $Objects,
                    "Data"      =>  $Data,
                    "Log"       =>  $Log
                ));        
    }        
    
}
