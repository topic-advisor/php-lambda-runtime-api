<?php

namespace TopicAdvisor\Lambda\RuntimeApi;

interface InvocationResponseInterface
{
    /**
     * @return string
     */
    public function getInvocationId(): string;

    /**
     * @return array
     */
    public function getPayload(): array;

    /**
     * @return bool
     */
    public function isError(): bool;
}