<?php

namespace TopicAdvisor\Lambda\RuntimeApi\Event\Record;

class EventRecord implements EventRecordInterface
{
    /** @var array */
    protected $payload;

    public function __construct(array $payload)
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
}