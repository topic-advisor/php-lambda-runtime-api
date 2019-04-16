<?php

namespace TopicAdvisor\Lambda\RuntimeApi;

interface InvocationRequestHandlerInterface
{
    /**
     * @param InvocationRequestInterface $request
     * @return bool
     */
    public function canHandle(InvocationRequestInterface $request) : bool;

    /**
     * @param InvocationRequestInterface $request
     * @return void
     */
    public function preHandle(InvocationRequestInterface $request);

    /**
     * @param InvocationRequestInterface $request
     * @return InvocationResponseInterface
     * @throws \Exception
     */
    public function handle(InvocationRequestInterface $request) : InvocationResponseInterface;

    /**
     * @param InvocationRequestInterface $request
     * @param InvocationResponseInterface $response
     * @return void
     */
    public function postHandle(InvocationRequestInterface $request, InvocationResponseInterface $response);
}