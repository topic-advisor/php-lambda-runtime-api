<?php

namespace TopicAdvisor\Lambda\RuntimeApi\Exception;

use TopicAdvisor\Lambda\RuntimeApi\HttpRequest\InvocationInterface;

class NoHandlerSpecifiedException extends \RuntimeException
{
    public function __construct(InvocationInterface $request)
    {
        parent::__construct(sprintf(
            'A handler has not been specified for request of type %s (payload: %s)',
            get_class($request),
            json_encode($request->getPayload())
        ));
    }
}