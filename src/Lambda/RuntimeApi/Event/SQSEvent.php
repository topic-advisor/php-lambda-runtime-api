<?php

namespace TopicAdvisor\Lambda\RuntimeApi\Event;

use TopicAdvisor\Lambda\RuntimeApi\Event\Record\SQSEventRecord;

class SQSEvent extends MultiEvent implements MultiEventInterface
{
    protected $eventRecordClass = SQSEventRecord::class;

    const EVENT_SOURCE = 'aws:sqs';
}