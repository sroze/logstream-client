<?php

namespace ContinuousPipe\LogStream\Serializer;

use ContinuousPipe\LogStream\Log;

class JsonLogSerializer implements LogSerializer
{
    /**
     * {@inheritdoc}
     */
    public function serialize(Log $log)
    {
        return json_encode([
            'type' => $log->getType(),
            'message' => $log->getMessage(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function deserialize($raw)
    {
        $json = json_decode($raw, true);

        return new Log($json['type'], $json['message']);
    }
}
