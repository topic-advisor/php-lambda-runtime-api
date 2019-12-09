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

use TopicAdvisor\Lambda\RuntimeApi\RuntimeApiLoop;


$loop = new RuntimeApiLoop();
$loop
    ->setHandlers([
        new App\ApplicationHandler(),
    ])
    ->run();
```

# Local Development

It is possible to develop locally against real Lambda requests.
This is done in the CLI iva an interactive client.

To run a local process loop:
 
1: Modify your `bootstrap.php` to use the Cli Client class for handling requests & responses:

```php
#!/opt/bin/php
<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use TopicAdvisor\Lambda\RuntimeApi\Client\CliClient;
use TopicAdvisor\Lambda\RuntimeApi\RuntimeApiLoop;

$options = [];
if (getenv('APP_LOCAL')) {
    if (!class_exists(Dotenv::class)) {
        throw new \RuntimeException('APP_ENV environment variable is not defined. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env file.');
    }
    (new Dotenv())->load(__DIR__.'/.env');
    $options[RuntimeApiLoop::OPTION_CLIENT_CLASS] = CliClient::class;
}

$loop = new TopicAdvisor\Lambda\RuntimeApi\RuntimeApiLoop($options);
$loop
    ->setHandlers([
        new App\ApplicationHandler(),
    ])
    ->run();
```

2: Execute the bootstrap file from the command line:

```
 $ APP_LOCAL=1 php bootstrap.php
```

3: At the prompt, enter a JSON-encoded Lambda request object, and hit enter twice

Note: requests can span multiple lines. The CLI Client will wait until it detects 2 empty lines.

```
Please enter a request:

{
  "body": "...",
  "resource": "/{proxy+}",
  "path": "/",
  "httpMethod": "GET",
  "isBase64Encoded": true,
  "queryStringParameters": {
    ...
  },
  "multiValueQueryStringParameters": {
    ...
  },
  "pathParameters": {
    ...
  },
  "stageVariables": {},
  "headers": {
    ...
  },
  "multiValueHeaders": {
    ...
  },
  "requestContext": {
    ...
  }
}


Response:

{
    "statusCode": 200,
    "multiValueHeaders": {
       ...
    },
    "headers": {
        ...
    },
    "body": "..."
}

```