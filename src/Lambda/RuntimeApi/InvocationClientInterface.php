<?php

namespace TopicAdvisor\Lambda\RuntimeApi;

use Psr\Log\LoggerInterface;

interface InvocationClientInterface
{
    /**
     * @param InvocationRequestFactory $requestFactory
     * @param LoggerInterface|null $logger
     * @throws \Throwable
     */
    public function __construct(InvocationRequestFactory $requestFactory);

    /**
     * @return InvocationRequestInterface
     * @throws \Throwable
     */
    public function getNextRequest(): InvocationRequestInterface;

    /**
     * @param InvocationResponseInterface $response
     * @return void
     * @throws \Throwable
     */
    public function sendResponse(InvocationResponseInterface $response);

    /**
     * @param \Throwable $exception
     * @param InvocationRequestInterface|null $request
     * @return bool
     */
    public function handleFailure(\Throwable $exception, InvocationRequestInterface $request = null): bool;
}