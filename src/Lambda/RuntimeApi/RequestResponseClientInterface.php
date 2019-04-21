<?php

namespace TopicAdvisor\Lambda\RuntimeApi;

use Psr\Log\LoggerInterface;

interface RequestResponseClientInterface
{
    /**
     * @param InvocationRequestFactory $requestFactory
     * @param LoggerInterface|null $logger
     */
    public function __construct(InvocationRequestFactory $requestFactory, LoggerInterface $logger = null);

    /**
     * @return InvocationRequestInterface
     */
    public function getNextRequest(): InvocationRequestInterface;

    /**
     * @param InvocationResponseInterface $response
     * @return void
     */
    public function sendResponse(InvocationResponseInterface $response);

    /**
     * @param \Throwable $exception
     * @param InvocationRequestInterface|null $request
     * @return void
     */
    public function handleFailure(\Throwable $exception, InvocationRequestInterface $request = null);
}