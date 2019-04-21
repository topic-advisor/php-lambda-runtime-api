<?php

namespace TopicAdvisor\Lambda\RuntimeApi;

use Psr\Log\LoggerInterface;
use TopicAdvisor\Lambda\RuntimeApi\Client\RuntimeApiClient;
use TopicAdvisor\Lambda\RuntimeApi\Client\StdIoClient;
use TopicAdvisor\Lambda\RuntimeApi\Exception\NoHandlerSpecifiedException;

class RuntimeApiLoop
{
    const OPTION_LOGGER = 'logger';
    const OPTION_IS_CLI = 'isCli';

    /** @var array */
    private $options;

    /** @var InvocationRequestHandlerInterface[] */
    private $handlers;

    /** @var RequestResponseClientInterface */
    private $requestResponseClient;

    /** @var LoggerInterface */
    private $logger;

    /** @var int */
    private $requestCount;

    /**
     * @param array $options
     * ['logger' => LoggerInterface, 'is_cli' => bool]
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
        $this->logger = $this->getOption(self::OPTION_LOGGER);
        if ($this->getOption(self::OPTION_IS_CLI)) {
            $this->requestResponseClient = new StdIoClient(new InvocationRequestFactory(), $this->logger);
        } else {
            $this->requestResponseClient = new RuntimeApiClient(new InvocationRequestFactory(), $this->logger);
        }
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

            if ($this->logger) {
                $this->logger->info('Request processed successfully', [
                    'requestCount' => $this->requestCount,
                    'memoryMaxMB' => (number_format(memory_get_peak_usage(true) / 1024 / 1024, 2)),
                    'memoryCurrentMB' => (number_format(memory_get_usage(true) / 1024 / 1024, 2)),
                ]);
            }
        } while (true);
    }

    private function processRequest()
    {
        $request = null;
        try {
            $request = $this->requestResponseClient->getNextRequest();

            $handled = false;
            foreach ($this->handlers as $handler) {
                if ($handler->canHandle($request)) {
                    $handler->preHandle($request);
                    $response = $handler->handle($request);
                    $this->requestResponseClient->sendResponse($response);
                    $handler->postHandle($request, $response);
                    $handled = true;
                    break;
                }
            }

            if (!$handled) {
                throw new NoHandlerSpecifiedException($request);
            }
        } catch (\Throwable $e) {
            $this->requestResponseClient->handleFailure($e, $request);
        }
    }

    /**
     * @param string $optionName
     * @return mixed|null
     */
    private function getOption(string $optionName)
    {
        return $this->options[$optionName] ?? null;
    }
}