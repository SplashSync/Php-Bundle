<?php

namespace Splash\Bundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Splash\Bundle\Events\Standalone\FormListingEvent;

class StandaloneFormType extends AbstractType
{
    use \Splash\Bundle\Models\Connectors\EventDispatcherAwareTrait;
    
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->setEventDispatcher($eventDispatcher);
    }

    /**
     * @abstract    Add Text Field to Edit Form
     *
     * @param   FormBuilderInterface $builder
     * @param   string               $name
     * @param   array                $options
     *
     * @return $this
     */
    public function addTextField(FormBuilderInterface $builder, string $name, array $options)
    {

        $builder->add(
            strtolower($name),
            TextType::class,
            array_merge_recursive(array("required" => false), $options)
        );
        
        return $this;
    }
    
    /**
     * @abstract    Build Connector Edit Form
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        //====================================================================//
        // Dispatch Object Listing Event
        $this->getEventDispatcher()->dispatch(FormListingEvent::NAME, new FormListingEvent($builder, $options));
//        $this
//                ->addTextField($builder, 'param1', $options)
//                ->addTextField($builder, 'param2', $options)
//                ->addTextField($builder, 'param3', $options)
//                ->addTextField($builder, 'param4', $options)
//            ;
    }
}
