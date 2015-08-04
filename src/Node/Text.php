<?php

namespace LogStream\Node;

use LogStream\LogNode;

class Text implements LogNode
{
    /**
     * @var string
     */
    private $text;

    /**
     * @param string $text
     */
    public function __construct($text)
    {
        $this->text = $text;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'type' => 'text',
            'contents' => $this->text,
        ];
    }
}
