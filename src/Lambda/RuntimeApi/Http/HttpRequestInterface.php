<?php

namespace TopicAdvisor\Lambda\RuntimeApi\Http;

use Psr\Http\Message\ServerRequestInterface;
use TopicAdvisor\Lambda\RuntimeApi\InvocationRequestInterface;

interface HttpRequestInterface extends InvocationRequestInterface, ServerRequestInterface
{
    /**
     * @return array
     */
    public function getContext(): array;

    /**
     * @return bool
     */
    public function isBase64Encoded();

    /**
     * @return bool
     */
    public function hasMultiValueParameters();
}