# php-lambda-runtime-api
A PHP library to wrap the AWS Lambda runtime API

# Installation

Use composer: `composer require topic-advisor/php-lambda-runtime-api`

# How it works

This library works by instantiating objects for each Lambda requests, and looping through the list
of provided request handlers to find a handler for the request. Once found, the handler processes the request
and returns a response.

# Usage

1: Create at least one handler to handle requests to your applications that implements
`TopicAdvisor\Lambda\RuntimeApi\InvocationRequestHandlerInterface`.

```php
<?php

namespace App;

use TopicAdvisor\Lambda\RuntimeApi\InvocationRequestHandlerInterface;
use TopicAdvisor\Lambda\RuntimeApi\InvocationRequestInterface;
use TopicAdvisor\Lambda\RuntimeApi\InvocationResponseInterface;
use TopicAdvisor\Lambda\RuntimeApi\Http\HttpRequestInterface;
use TopicAdvisor\Lambda\RuntimeApi\Http\HttpResponse;

class ApplicationHandler implements InvocationRequestHandlerInterface
{
    /**
     * @param InvocationRequestInterface $request
     * @return bool
     */
    public function canHandle(InvocationRequestInterface $request): bool
    {
        return $request instanceof HttpRequestInterface;
    }

    /**
     * @param InvocationRequestInterface $lambdaRequest
     * @return InvocationResponseInterface
     * @throws \Exception
     */
    public function handle(InvocationRequestInterface $lambdaRequest): InvocationResponseInterface
    {
        // Execute your application code and return an instance of InvocationResponseInterface
        
        $response = new HttpResponse($lambdaRequest->getInvocationId());
        $response->setStatusCode(200);
        $response->setHeaders(['content-type' => 'application/json']);
        $response->setBody('{"hello":"world"}');
        
        return $response;
    }
    
    /**
     * @param InvocationRequestInterface $request
     * @return void
     */
    public function preHandle(InvocationRequestInterface $request)
    {
        // Do any pre-request handling
    }

    /**
     * @param InvocationRequestInterface $request
     * @param InvocationResponseInterface $response
     * @return void
     */
    public function postHandle(InvocationRequestInterface $request, InvocationResponseInterface $response)
    {
        // Do any post-request handling such as unsetting variables, resetting services, etc
    }
}

```

2: Create a `bootstrap` file in your project root folder

```php
#!/opt/bin/php
<?php

require __DIR__ . '/vendor/autoload.php';

$loop = new TopicAdvisor\Lambda\RuntimeApi\RuntimeApiLoop();
$loop
    ->setHandlers([
        new App\ApplicationHandler(),
    ])
    ->run();
```

# PHP Configuration

You can include a PHP configuration by providing a `php.ini` file in the root of your project and changing the
shebang line in `bootstrap` to:
```
#!/opt/bin/php -cphp.ini
```