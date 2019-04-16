<?php

namespace TopicAdvisor\Lambda\RuntimeApi;

use TopicAdvisor\Lambda\RuntimeApi\Event\SQSEvent;
use TopicAdvisor\Lambda\RuntimeApi\Exception\UnsupportedRequestException;
use TopicAdvisor\Lambda\RuntimeApi\Http\Request\APIGatewayRequest;
use TopicAdvisor\Lambda\RuntimeApi\Http\Request\ELBRequest;

class InvocationRequestFactory
{
    /**
     * @param string $invocationId
     * @param array $payload
     * @return InvocationRequestInterface
     */
    public function getRequest(string $invocationId, array $payload): InvocationRequestInterface
    {
        switch (true) {
            // Http Requests
            case isset($payload['requestContext']):
                switch (true) {
                    case isset($payload['requestContext']['elb']):
                        return new ELBRequest($invocationId, $payload);
                    case isset($payload['requestContext']['apiId']):
                        return new APIGatewayRequest($invocationId, $payload);
                }
                break;

            // Events
            case isset($payload['Records'][0]['eventSource']):
                switch (true) {
                    case $payload['Records'][0]['eventSource'] === SQSEvent::EVENT_SOURCE:
                        return new SQSEvent($invocationId, $payload);
                }
                break;
        }

        throw new UnsupportedRequestException($payload);
    }
}