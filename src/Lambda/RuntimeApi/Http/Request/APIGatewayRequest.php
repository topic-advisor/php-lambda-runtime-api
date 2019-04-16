<?php

namespace TopicAdvisor\Lambda\RuntimeApi\Http\Request;

use TopicAdvisor\Lambda\RuntimeApi\Http\HttpRequest;

class APIGatewayRequest extends HttpRequest
{
    /**
     * @return array
     */
    public function getStageVariables(): array
    {
        return $this->payload['stageVariables'] ?? [];
    }

    /**
     * @return null|string
     */
    public function getResource(): ?string
    {
        return $this->payload['resource'] ?? null;
    }

    /**
     * @return array
     */
    public function getPathParameters(): array
    {
        return $this->payload['pathParameters'] ?? [];
    }

    /**
     * @return null|string
     */
    public function getApiId(): ?string
    {
        return $this->payload['requestContext']['apiId'] ?? null;
    }
}