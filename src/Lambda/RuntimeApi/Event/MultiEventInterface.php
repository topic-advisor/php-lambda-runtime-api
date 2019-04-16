<?php

namespace TopicAdvisor\Lambda\RuntimeApi\Event;

interface MultiEventInterface extends EventInterface
{
    /**
     * @return EventRecordInterface[]
     */
    public function getRecords(): array;
}