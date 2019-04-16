<?php

namespace TopicAdvisor\Lambda\RuntimeApi\Exception;

class UnsupportedRequestException extends \RuntimeException
{
    public function __construct(array $payload)
    {
        parent::__construct('Request is not supported (payload: ' . json_encode($payload) .')');
    }
}