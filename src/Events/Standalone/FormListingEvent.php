<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Events\Standalone;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Standalone Form Listing Event
 * This Event is Triggered by Standalone Connector to Populat Configuration Form for Local Connectors
 */
class FormListingEvent extends Event
{
    /**
     * Event Name
     */
    const NAME = "Splash\\Bundle\\Events\\Standalone\\FormListingEvent";

    /**
     * @var FormBuilderInterface
     */
    protected FormBuilderInterface $builder;

    /**
     * @var array
     */
    protected array $options = array();

    /**
     * Event Constructor
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function __construct(FormBuilderInterface $builder, array $options)
    {
        $this->builder = $builder;
        $this->options = $options;
    }

    /**
     * Get Form Builder
     *
     * @return FormBuilderInterface
     */
    public function getBuilder(): FormBuilderInterface
    {
        return $this->builder;
    }

    /**
     * Get Form Options
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
