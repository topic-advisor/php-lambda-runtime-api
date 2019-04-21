<?php

namespace TopicAdvisor\Lambda\RuntimeApi\Client;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use TopicAdvisor\Lambda\RuntimeApi\InvocationRequestFactory;
use TopicAdvisor\Lambda\RuntimeApi\InvocationRequestInterface;
use TopicAdvisor\Lambda\RuntimeApi\InvocationResponseInterface;
use TopicAdvisor\Lambda\RuntimeApi\RequestResponseClientInterface;

class RuntimeApiClient implements RequestResponseClientInterface
{
    /** @var InvocationRequestFactory */
    private $requestFactory;

    /** @var Client */
    private $httpClient;

    /** @var LoggerInterface */
    private $logger;

    /**
     * RuntimeApiClient constructor.
     * @param InvocationRequestFactory $requestFactory
     * @param LoggerInterface|null $logger
     */
    public function __construct(InvocationRequestFactory $requestFactory, LoggerInterface $logger = null)
    {
        $this->requestFactory = $requestFactory;
        $this->httpClient = new Client([
            'base_uri' => getenv('AWS_LAMBDA_RUNTIME_API'),
            'timeout' => 0,
        ]);
        $this->logger = $logger;
    }

    /**
     * @return InvocationRequestInterface
     */
    public function getNextRequest(): InvocationRequestInterface
    {
        $invocation = $this->httpClient->get('/2018-06-01/runtime/invocation/next');
        $invocationId = $invocation->getHeader('Lambda-Runtime-Aws-Request-Id')[0];
        $payload = json_decode((string) $invocation->getBody(), true);

        return $this->requestFactory->getRequest($invocationId, $payload);
    }

    /**
     * @param InvocationResponseInterface $response
     * @return void
     */
    public function sendResponse(InvocationResponseInterface $response)
    {
        $this->httpClient->post("/2018-06-01/runtime/invocation/{$response->getInvocationId()}/response", [
            'json' => $response->getPayload()
        ]);
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
                $this->httpClient->post("/2018-06-01/runtime/invocation/{$request->getInvocationId()}/error", [
                    'json' => ['error' => $exception->getMessage()],
                ]);
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