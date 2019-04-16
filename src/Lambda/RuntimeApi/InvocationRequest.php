<?php

namespace TopicAdvisor\Lambda\RuntimeApi;

class InvocationRequest implements InvocationRequestInterface
{
    /** @var string */
    protected $invocationId;

    /** @var array */
    protected $payload;

    /**
     * @param string $invocationId
     * @param array $payload
     */
    public function __construct(string $invocationId, array $payload)
    {
        $this->invocationId = $invocationId;
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function getInvocationId(): string
    {
        return $this->invocationId;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
}