<?php

namespace LogStream;

interface Log extends LogNode
{
    const RUNNING = 'running';

    public function isStarted();

    public function getId();

    public function getStatus();
}
