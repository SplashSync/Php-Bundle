<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Form;

use Splash\Bundle\Events\Standalone\FormListingEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Standalone Connector Edit Form
 */
class StandaloneFormType extends AbstractType
{
    use \Splash\Bundle\Models\Connectors\EventDispatcherAwareTrait;

    /**
     * Form Constructor
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->setEventDispatcher($eventDispatcher);
    }

    /**
     * Add Text Field to Edit Form
     *
     * @param FormBuilderInterface $builder
     * @param string               $name
     * @param array                $options
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
     * Build Connector Edit Form
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //====================================================================//
        // Dispatch Object Listing Event
        $this->getEventDispatcher()->dispatch(FormListingEvent::NAME, new FormListingEvent($builder, $options));
    }
}
