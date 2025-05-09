<?php

namespace Splash\Bundle\Form\Container;

class KeyValueContainer implements \ArrayAccess
{
    private array $data;

    public function __construct(array $data = array())
    {
        $this->data = $data;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return (is_integer($offset) || is_string($offset)) && array_key_exists($offset, $this->data);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset): mixed
    {
        return $this->data[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->data[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }
}
 