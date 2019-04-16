<?php

namespace TopicAdvisor\Lambda\RuntimeApi\Http\Request;

use TopicAdvisor\Lambda\RuntimeApi\Http\HttpRequest;

class ELBRequest extends HttpRequest
{
    /**
     * @return array
     */
    public function getELBContext(): array
    {
        return $this->payload['requestContext']['elb'] ?? [];
    }
}