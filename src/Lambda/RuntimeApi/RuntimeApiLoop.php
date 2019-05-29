<?php

namespace TopicAdvisor\Lambda\RuntimeApi;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use TopicAdvisor\Lambda\RuntimeApi\Client\RuntimeApiClient;
use TopicAdvisor\Lambda\RuntimeApi\Exception\NoHandlerSpecifiedException;

class RuntimeApiLoop
{
    const OPTION_LOGGER = 'logger';
    const OPTION_CLIENT_CLASS = 'clientClass';

    /** @var array */
    private $options;

    /** @var InvocationRequestHandlerInterface[] */
    private $handlers;

    /** @var InvocationClientInterface */
    private $invocationClient;

    /** @var LoggerInterface */
    private $logger;

    /** @var int */
    private $requestCount;

    /** @var float */
    private $requestTime;

    /** @var float */
    private $lastMemoryMb;

    /**
     * @param array $options
     * ['logger' => LoggerInterface, 'is_cli' => bool]
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
        $this->invocationClient = $this->createInvocationClient();
        $this->logger = $this->getOption(self::OPTION_LOGGER);
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

            $currentMemory = memory_get_usage(true) / 1024 / 1024;
            $this->log(LogLevel::INFO, 'Request processed successfully', [
                'requestCount' => ++$this->requestCount,
                'requestTimeMs' => number_format($this->requestTime, 2),
                'memoryMaxMB' => number_format(memory_get_peak_usage(true) / 1024 / 1024, 2),
                'memoryCurrentMB' => number_format($currentMemory, 2),
                'memoryLeakMb' => number_format($this->lastMemoryMb ? $currentMemory - $this->lastMemoryMb : 0, 2),
            ]);
            $this->lastMemoryMb = $currentMemory;
        } while (true);
    }

    private function processRequest()
    {
        $request = null;
        try {
            $request = $this->invocationClient->getNextRequest();
            $startTime = microtime(true);

            $handled = false;
            foreach ($this->handlers as $handler) {
                if ($handler->canHandle($request)) {
                    $handler->preHandle($request);
                    $response = $handler->handle($request);
                    $this->invocationClient->sendResponse($response);
                    $handler->postHandle($request, $response);
                    $handled = true;
                    break;
                }
            }

            if (!$handled) {
                throw new NoHandlerSpecifiedException($request);
            }

            $this->requestTime = (microtime(true) - $startTime) * 1000;
        } catch (\Throwable $e) {
            $this->fail($e, $request);
        }
    }

    /**
     * @param \Throwable $exception
     * @param InvocationRequestInterface|null $request
     * @throws \Throwable
     */
    private function fail(\Throwable $exception, InvocationRequestInterface $request = null)
    {
        $this->log(LogLevel::ERROR, $exception->getMessage(), $exception->getTrace());
        if (!$this->invocationClient->handleFailure($exception, $request)) {
            throw $exception;
        }
    }

    /**
     * @return InvocationClientInterface
     */
    private function createInvocationClient(): InvocationClientInterface
    {
        $clientClass = $this->getOption(self::OPTION_CLIENT_CLASS, RuntimeApiClient::class);
        return new $clientClass(new InvocationRequestFactory(), $this->logger);
    }

    /**
     * @param string $optionName
     * @param mixed|null $default
     * @return mixed|null
     */
    private function getOption(string $optionName, $default = null)
    {
        return $this->options[$optionName] ?? $default;
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     */
    private function log(string $level, string $message, array $context = [])
    {
        if ($this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }
}