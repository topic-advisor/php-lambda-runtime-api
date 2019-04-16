<?php

namespace TopicAdvisor\Lambda\RuntimeApi\Event\Record;

interface IdentifiableEventRecordInterface extends EventRecordInterface
{
    public function getId(): string;
}