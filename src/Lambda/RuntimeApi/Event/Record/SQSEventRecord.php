<?php

namespace TopicAdvisor\Lambda\RuntimeApi\Event\Record;

class SQSEventRecord extends EventRecord implements IdentifiableEventRecordInterface
{
    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->payload['messageId'];
    }

    /**
     * @return null|string
     */
    public function getReceiptHandle(): ?string
    {
        return $this->payload['receiptHandle'] ?? null;
    }

    /**
     * @return null|string
     */
    public function getBody(): ?string
    {
        return $this->payload['body'] ?? null;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->payload['attributes'] ?? [];
    }

    /**
     * @return array
     */
    public function getMessageAttributes(): array
    {
        return $this->payload['messageAttributes'] ?? [];
    }

    /**
     * @return null|string
     */
    public function getMd5OfBody(): ?string
    {
        return $this->payload['md5OfBody'] ?? null;
    }

    /**
     * @return null|string
     */
    public function getEventSourceArn(): ?string
    {
        return $this->payload['eventSourceARN'] ?? null;
    }

    public function getAwsRegion(): ?string
    {
        return $this->payload['awsRegion'] ?? null;
    }
}