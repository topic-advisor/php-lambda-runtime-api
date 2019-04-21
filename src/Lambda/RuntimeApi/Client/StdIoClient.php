<?php

namespace TopicAdvisor\Lambda\RuntimeApi\Client;

use Psr\Log\LoggerInterface;
use TopicAdvisor\Lambda\RuntimeApi\InvocationRequestFactory;
use TopicAdvisor\Lambda\RuntimeApi\InvocationRequestInterface;
use TopicAdvisor\Lambda\RuntimeApi\InvocationResponseInterface;
use TopicAdvisor\Lambda\RuntimeApi\RequestResponseClientInterface;

class StdIoClient implements RequestResponseClientInterface
{
    /** @var InvocationRequestFactory */
    private $requestFactory;

    /** @var LoggerInterface */
    private $logger;

    /** @var resource */
    private $stdIn;

    /** @var resource */
    private $stdOut;

    /**
     * RuntimeApiClient constructor.
     * @param InvocationRequestFactory $requestFactory
     * @param LoggerInterface|null $logger
     */
    public function __construct(InvocationRequestFactory $requestFactory, LoggerInterface $logger = null)
    {
        $this->requestFactory = $requestFactory;
        $this->logger = $logger;
        $this->stdIn = fopen('php://stdin', 'r');
        $this->stdOut = fopen('php://stdout', 'w');

        if (false === $this->stdIn || false === $this->stdOut) {
            throw new \RuntimeException('Unable to open stdin and/or stdout');
        }
    }

    /**
     * @return InvocationRequestInterface
     */
    public function getNextRequest(): InvocationRequestInterface
    {
        $input = fgets($this->stdIn);
        $payload = json_decode((string) $input, true);

        return $this->requestFactory->getRequest(uniqid('stdio-'), $payload);
    }

    /**
     * @param InvocationResponseInterface $response
     * @return void
     */
    public function sendResponse(InvocationResponseInterface $response)
    {
        fwrite($this->stdOut, json_encode($response->getPayload()));
    }

    /**
     * @param \Throwable $exception
     * @param InvocationRequestInterface|null $request
     * @return void
     */
    public function handleFailure(\Throwable $exception, InvocationRequestInterface $request = null)
    {
        // Log error
        if ($this->logger) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
        }

        if ($request) {
            try {
                fwrite($this->stdOut, json_encode(['error' => $exception->getMessage()]));
            } catch (\Throwable $exception) {
                if ($this->logger) {
                    $this->logger->error(
                        sprintf("Unable to respond to error endpoint. (%s)"), $exception->getMessage(),
                        $exception->getTrace()
                    );
                }
            }
        }

        exit(1);
    }
}