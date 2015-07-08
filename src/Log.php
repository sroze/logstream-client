<?php

namespace ContinuousPipe\LogStream;

class Log
{
    const TYPE_ERROR = 'error';
    const TYPE_OUTPUT = 'output';

    private $type;
    private $message;

    private function __construct($type, $message)
    {
        $this->type = $type;
        $this->message = $message;
    }

    public static function error($message)
    {
        return new self(self::TYPE_ERROR, $message);
    }

    public static function output($message)
    {
        return new self(self::TYPE_OUTPUT, $message);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
