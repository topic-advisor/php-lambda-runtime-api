<?php

namespace TopicAdvisor\Lambda\RuntimeApi\Http;

use Psr\Http\Message\ResponseInterface;
use TopicAdvisor\Lambda\RuntimeApi\InvocationResponseInterface;

interface HttpResponseInterface extends InvocationResponseInterface, ResponseInterface
{
    /**
     * @return bool
     */
    public function isBase64Encoded(): bool;
}