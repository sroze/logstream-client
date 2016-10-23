<?php

namespace LogStream\Node;

class Complex implements Node
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $data;

    /**
     * @param string $type
     * @param array $data
     */
    public function __construct($type, array $data = [])
    {
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return array_merge([
            'type' => $this->type,
        ], $this->data);
    }
}
