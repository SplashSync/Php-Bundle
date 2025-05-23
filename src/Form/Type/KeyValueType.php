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

namespace Splash\Bundle\Form\Type;

use Splash\Bundle\Form\DataTransformer\HashToKeyValueArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

/**
 * Key Value Form Type
 */
class KeyValueType extends AbstractType
{
    /**
     * Builds the form by adding a model transformer and event listener to process the data.
     *
     * The added model transformer converts the input data based on the provided `use_container_object` option.
     * An event listener is attached to the `PRE_SET_DATA` event to transform the input associative array into
     * an array of key-value pairs before setting the form data.
     *
     * @param FormBuilderInterface $builder The form builder.
     * @param array                $options The array of options for the form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(
            new HashToKeyValueArrayTransformer($options['use_container_object'])
        );

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $e) {
            $input = $e->getData();

            if (!is_iterable($input)) {
                return;
            }

            $output = array();

            foreach ($input as $key => $value) {
                $output[] = array(
                    'key' => $key,
                    'value' => $value
                );
            }

            $e->setData($output);
        }, 1);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'prototype' => true,
            'entry_type' => KeyValueRowType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'key_type' => TextType::class,
            'key_options' => array(),
            'value_options' => array(),
            'allowed_keys' => null,
            'use_container_object' => false,
            'entry_options' => function (Options $options) {
                return array(
                    'key_type' => $options['key_type'],
                    'value_type' => $options['value_type'],
                    'key_options' => $options['key_options'],
                    'value_options' => $options['value_options'],
                    'allowed_keys' => $options['allowed_keys']
                );
            }
        ));

        $resolver->setRequired(array('value_type'));
        $resolver->setAllowedTypes('allowed_keys', array('null', 'array'));
    }

    /**
     * @inheritdoc
     */
    public function getParent(): ?string
    {
        return class_exists(LiveCollectionType::class)
            ? LiveCollectionType::class
            : CollectionType::class
        ;
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix(): string
    {
        return 'burgov_key_value';
    }
}
