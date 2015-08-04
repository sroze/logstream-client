<?php

namespace LogStream;

interface Log
{
    const RUNNING = 'running';

    public function isStarted();

    public function jsonSerialize();

    public function getId();
}
