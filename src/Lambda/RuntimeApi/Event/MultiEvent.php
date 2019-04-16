<?php

namespace TopicAdvisor\Lambda\RuntimeApi\Event;

use TopicAdvisor\Lambda\RuntimeApi\Event\Record\EventRecord;
use TopicAdvisor\Lambda\RuntimeApi\Event\Record\EventRecordInterface;

class MultiEvent extends Event implements MultiEventInterface
{
    /** @var string */
    protected $eventRecordClass = EventRecord::class;

    /** @var array */
    protected $events;

    /**
     * @param string $invocationId
     * @param array $payload
     */
    public function __construct(string $invocationId, array $payload)
    {
        parent::__construct($invocationId, $payload);

        $this->events = [];
        foreach ($this->payload['Records'] as $recordData) {
            $this->events[] = $this->toEventRecord($recordData);
        }
    }

    /**
     * @return EventRecordInterface[]
     */
    public function getRecords(): array
    {
        return $this->events;
    }

    /**
     * @param array $payload
     * @return EventRecordInterface
     */
    protected function toEventRecord(array $payload): EventRecordInterface
    {
        return new $this->eventRecordClass($payload);
    }
}