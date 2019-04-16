<?php

namespace TopicAdvisor\Lambda\RuntimeApi;

use GuzzleHttp\Client;
use TopicAdvisor\Lambda\RuntimeApi\Exception\NoHandlerSpecifiedException;

class RuntimeApiLoop
{
    /** @var array */
    private $options;

    /**
     * @var InvocationRequestHandlerInterface[]
     */
    private $handlers;

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var InvocationRequestFactory
     */
    private $requestFactory;

    /**
     * @var int
     */
    private $requestCount;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
        $this->httpClient = new Client([
            'base_uri' => getenv('AWS_LAMBDA_RUNTIME_API'),
            'timeout' => 0,
        ]);
        $this->requestFactory = new InvocationRequestFactory();
        $this->requestCount = 0;
    }

    /**
     * @param array $handlers
     * @return RuntimeApiLoop
     */
    public function setHandlers(array $handlers): RuntimeApiLoop
    {
        $this->handlers = $handlers;

        return $this;
    }

    public function run()
    {
        do {
            $this->processRequest();

            gc_collect_cycles();

            $this->requestCount++;
            echo "INFO: Request count: {$this->requestCount}; ";
            echo "Max memory usage: " . (number_format(memory_get_peak_usage(true) / 1024 / 1024, 2)) . "MB; ";
            echo "Current memory: " . (number_format(memory_get_usage(true) / 1024 / 1024, 2)) . "MB; ";
            echo "\n";
        } while (true);
    }

    private function processRequest()
    {
        $request = null;
        try {
            $request = $this->getNextRequest();

            $handled = false;
            foreach ($this->handlers as $handler) {
                if ($handler->canHandle($request)) {
                    $handler->preHandle($request);
                    $response = $handler->handle($request);
                    $this->sendResponse($response);
                    $handler->postHandle($request, $response);
                    $handled = true;
                    break;
                }
            }

            if (!$handled) {
                throw new NoHandlerSpecifiedException($request);
            }
        } catch (\Throwable $e) {
            $this->fail($e, $request);
        }
    }

    /**
     * @return InvocationRequestInterface
     */
    private function getNextRequest(): InvocationRequestInterface
    {
        $invocation = $this->httpClient->get('/2018-06-01/runtime/invocation/next');
        $invocationId = $invocation->getHeader('Lambda-Runtime-Aws-Request-Id')[0];
        $payload = json_decode((string) $invocation->getBody(), true);

        return $this->requestFactory->getRequest($invocationId, $payload);
    }

    /**
     * @param InvocationResponseInterface $response
     */
    private function sendResponse(InvocationResponseInterface $response)
    {
        $this->httpClient->post("/2018-06-01/runtime/invocation/{$response->getInvocationId()}/response", [
            'json' => $response->getPayload()
        ]);
    }

    /**
     * @param InvocationRequestInterface $request
     * @param \Throwable $exception
     */
    private function fail(\Throwable $exception, InvocationRequestInterface $request = null)
    {
        // Log error
        echo "ERROR: {$exception->getMessage()}\n";
        echo "{$exception->getTraceAsString()}\n";

        if ($request) {
            try {
                $this->httpClient->post("/2018-06-01/runtime/invocation/{$request->getInvocationId()}/error", [
                    'json' => ['error' => $exception->getMessage()],
                ]);
            } catch (\Throwable $exception) {
                echo "ERROR: Unable to respond to error endpoint. ({$exception->getMessage()})\n";
                echo "{$exception->getTraceAsString()}\n";
            }
        }

        exit(1);
    }
}