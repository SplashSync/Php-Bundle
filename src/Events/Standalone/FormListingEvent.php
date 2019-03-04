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

namespace Splash\Bundle\Events\Standalone;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Standalone Form Listing Event
 * This Event is Triggered by Standalone Connector to Populat Configuration Form for Local Connectors
 */
class FormListingEvent extends Event
{
    /**
     * Event Name
     */
    const NAME = "splash.standalone.list.form";

    /**
     * @var FormBuilderInterface
     */
    protected $builder;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @abstract    Event Constructor
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
     * @abstract    Get Form Builder
     *
     * @return FormBuilderInterface
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @abstract    Get Form Options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
