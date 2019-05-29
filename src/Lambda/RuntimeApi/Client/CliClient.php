<?php

namespace TopicAdvisor\Lambda\RuntimeApi\Client;

use Psr\Log\LoggerInterface;
use TopicAdvisor\Lambda\RuntimeApi\InvocationRequestFactory;
use TopicAdvisor\Lambda\RuntimeApi\InvocationRequestInterface;
use TopicAdvisor\Lambda\RuntimeApi\InvocationResponseInterface;
use TopicAdvisor\Lambda\RuntimeApi\InvocationClientInterface;

class CliClient implements InvocationClientInterface
{
    /** @var InvocationRequestFactory */
    private $requestFactory;

    /** @var resource */
    private $stdIn;

    /** @var resource */
    private $stdOut;

    /**
     * RuntimeApiClient constructor.
     * @param InvocationRequestFactory $requestFactory
     */
    public function __construct(InvocationRequestFactory $requestFactory)
    {
        $this->requestFactory = $requestFactory;
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
        fwrite($this->stdOut, "\nPlease enter a request:\n\n");
        $input = '';
        do {
            $line = trim(fgets($this->stdIn));
            if ($line) {
                $input .= $line . "\n";
            }
        } while (!$input || $line);
        $payload = json_decode((string) $input, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException(sprintf('Invalid payload: %s', json_last_error_msg()));
        }

        fwrite($this->stdOut, "\nProcessing response...\n\n");

        return $this->requestFactory->getRequest(uniqid('stdio-'), $payload);
    }

    /**
     * @param InvocationResponseInterface $response
     * @return void
     */
    public function sendResponse(InvocationResponseInterface $response)
    {
        fwrite($this->stdOut, "\nResponse:\n\n");

        fwrite($this->stdOut, json_encode($response->getPayload(), JSON_PRETTY_PRINT) . "\n\n");
    }

    /**
     * @param \Throwable $exception
     * @param InvocationRequestInterface|null $request
     * @return bool
     */
    public function handleFailure(\Throwable $exception, InvocationRequestInterface $request = null): bool
    {
        fwrite($this->stdOut, json_encode(['error' => $exception->getMessage()]));

        return true;
    }
}