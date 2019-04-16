<?php

namespace TopicAdvisor\Lambda\RuntimeApi;

class InvocationResponse implements InvocationResponseInterface
{
    /** @var string */
    protected $invocationId;

    /** @var array */
    protected $payload;

    /** @var bool */
    protected $isError;

    /**
     * @param string $invocationId
     * @param array $payload
     * @param bool $isError
     */
    public function __construct(string $invocationId, array $payload = [], bool $isError = false)
    {
        $this->invocationId = $invocationId;
        $this->payload = $payload;
        $this->isError = $isError;
    }

    /**
     * @return string
     */
    public function getInvocationId(): string
    {
        return $this->invocationId;
    }

    /**
     * @param array $payload
     */
    public function setPayload(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @param bool $isError
     */
    public function setIsError(bool $isError)
    {
        $this->isError = $isError;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->isError;
    }
}