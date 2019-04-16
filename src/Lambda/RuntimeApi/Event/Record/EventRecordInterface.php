<?php

namespace TopicAdvisor\Lambda\RuntimeApi\Event\Record;

interface EventRecordInterface
{
    /**
     * @return array
     */
    public function getPayload(): array;
}