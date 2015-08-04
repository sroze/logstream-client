<?php

namespace LogStream\Log;

class Text
{
    /**
     * @var string
     */
    private $contents;

    /**
     * @param string $contents
     */
    public function __construct($contents)
    {
        $this->contents = $contents;
    }

    public function getContents()
    {
        return $this->contents;
    }
}
