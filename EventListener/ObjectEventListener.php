<?php

namespace Splash\Bundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Splash\Client\Splash;

class ObjectEventListener
{
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $this->doCommit($eventArgs->getEntity(), SPL_A_CREATE);
    }    
    
    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        $this->doCommit($eventArgs->getEntity(), SPL_A_UPDATE);
    }    

    public function preRemove(LifecycleEventArgs $eventArgs)
    {
        $this->doCommit($eventArgs->getEntity(), SPL_A_DELETE);
    }    

    public function doCommit($Entity, $Action)
    {
        //====================================================================//
        // Check if Object is Mapped
        $ObjectType =   Splash::Local()->getObjectType(get_class($Entity)); 
        if ( is_null($ObjectType) ) {
            return;
        }
        //====================================================================//
        // Safety Check 
        if ( empty($Entity->getId()) || !is_scalar($Entity->getId()) ) {
            return;
        }
        //====================================================================//
        // TODO : Detect User & Setup Change commit comments
        //====================================================================//
        // Commit Change to Server 
        Splash::Commit($ObjectType, $Entity->getId(), $Action, "Symfony", "Change Commited on Doctrine ORM");
    }
}
