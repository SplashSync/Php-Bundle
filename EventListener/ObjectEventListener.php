<?php

namespace Splash\Bundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Splash\Client\Splash;

class ObjectEventListener
{
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        if (!Splash::local()->isListnerDisabled(__FUNCTION__)) {
            $this->doCommit($eventArgs->getEntity(), SPL_A_CREATE);
        }
    }
    
    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        if (!Splash::local()->isListnerDisabled(__FUNCTION__)) {
            $this->doCommit($eventArgs->getEntity(), SPL_A_UPDATE);
        }
    }

    public function preRemove(LifecycleEventArgs $eventArgs)
    {
        if (!Splash::local()->isListnerDisabled(__FUNCTION__)) {
            $this->doCommit($eventArgs->getEntity(), SPL_A_DELETE);
        }
    }

    public function doCommit($Entity, $Action)
    {
        //====================================================================//
        // Check if Object is Mapped
        $ObjectType =   Splash::local()->getObjectType($Entity);
        if (is_null($ObjectType)) {
            return;
        }
        //====================================================================//
        // Safety Check
        if (empty($Entity->getId()) || !is_scalar($Entity->getId())) {
            return;
        }
        //====================================================================//
        // Commit Change to Server
//        Splash::log()->deb("Commit " . $Action . " for " . $ObjectType . " ID " . $Entity->getId());
        Splash::Commit($ObjectType, $Entity->getId(), $Action, "Symfony", "Change Commited on Doctrine ORM");
        //====================================================================//
        // Render User Messages
        Splash::local()->pushNotifications();
    }
}
