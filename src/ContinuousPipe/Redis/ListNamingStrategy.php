<?php

namespace ContinuousPipe\LogStream\Redis;

use ContinuousPipe\LogStream\LogRelatedObject;

class ListNamingStrategy
{
    public function getListName(LogRelatedObject $object)
    {
        return $object->getIdentifier();
    }
}
