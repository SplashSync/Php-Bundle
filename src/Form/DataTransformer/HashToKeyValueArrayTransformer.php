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

namespace Splash\Bundle\Form\DataTransformer;

use Splash\Bundle\Form\Container\KeyValueContainer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class HashToKeyValueArrayTransformer implements DataTransformerInterface
{
    /**
     * @param bool $useContainerObject Whether to return a KeyValueContainer object or simply an array
     */
    public function __construct(private readonly bool $useContainerObject)
    {
    }

    /**
     * Doing the transformation here would be too late for the collection type to do it's resizing magic, so
     * instead it is done in the forms PRE_SET_DATA listener
     */
    public function transform(mixed $value): mixed
    {
        return $value;
    }

    /**
     * Execute the reverse transformation.
     *
     * @throws TransformationFailedException If the given value is not an array or if the array contains invalid data.
     */
    public function reverseTransform(mixed $value): KeyValueContainer|array
    {
        $return = $this->useContainerObject ? new KeyValueContainer() : array();

        if (!is_iterable($value)) {
            throw new TransformationFailedException;
        }

        foreach ($value as $data) {
            if (array('key', 'value') != array_keys($data)) {
                throw new TransformationFailedException;
            }

            if (isset($return[$data['key']])) {
                throw new TransformationFailedException('Duplicate key detected');
            }

            $return[$data['key']] = $data['value'];
        }

        return $return;
    }
}
