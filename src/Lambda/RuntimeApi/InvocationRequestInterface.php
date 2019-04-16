<?php

namespace TopicAdvisor\Lambda\RuntimeApi;

interface InvocationRequestInterface
{
    /**
     * @param string $invocationId
     * @param array $payload
     */
    public function __construct(string $invocationId, array $payload);

    /**
     * @return string
     */
    public function getInvocationId(): string;

    /**
     * @return array
     */
    public function getPayload(): array;
}