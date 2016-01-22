<?php

namespace LogStream\Node;

class Text implements Node
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

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }
}
