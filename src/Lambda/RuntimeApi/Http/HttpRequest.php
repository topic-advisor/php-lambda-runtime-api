<?php

namespace TopicAdvisor\Lambda\RuntimeApi\Http;

use function GuzzleHttp\Psr7\build_query;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use TopicAdvisor\Lambda\RuntimeApi\InvocationRequest;
use TopicAdvisor\Lambda\RuntimeApi\Http\Psr7\Stream;

class HttpRequest extends InvocationRequest implements HttpRequestInterface
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';
    const METHOD_OPTIONS = 'OPTIONS';

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->payload['requestContext'] ?? [];
    }

    /**
     * @return string
     */
    public function getProtocolVersion()
    {
        // TODO: Implement getProtocolVersion() method.
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return strtoupper($this->payload['httpMethod']) ?? null;
    }

    /**
     * @return Uri
     */
    public function getUri()
    {
        return new Uri($this->getRequestTarget());
    }

    /**
     * @return string
     */
    public function getRequestTarget()
    {
        $scheme = $this->getHeader('x-original-proto')[0] ?? '';
        $host = $this->getHeader('host')[0];
        if ($port = $this->getHeader('x-original-port')[0] ?? '') {
            $host .= ':' . $port;
        }
        $path = $this->payload['path'] ?? '';
        $query = build_query($this->getQueryParams());

        return Uri::composeComponents($scheme, $host, $path, $query, '');
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->payload['multiValueHeaders'] ?? $this->payload['headers'] ?? [];
    }

    /**
     * @param string $name
     * @return string[]
     */
    public function getHeader($name)
    {
        $header = $this->getHeaders()[$name] ?? $this->getHeaders()[strtolower($name)] ?? [];
        return is_array($header) ? $header : [$header];
    }

    /**
     * @param string $name
     * @return string
     */
    public function getHeaderLine($name)
    {
        return implode(',', $this->getHeader($name));
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasHeader($name)
    {
        return array_key_exists($name, $this->getHeaders()) || array_key_exists(strtolower($name), $this->getHeaders());
    }

    /**
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->payload['multiValueQueryStringParameters'] ?? $this->payload['queryStringParameters'] ?? [];
    }

    /**
     * @return StreamInterface
     */
    public function getBody()
    {
        $body = $this->payload['body'] ?? '';
        return new Stream($this->isBase64Encoded() ? base64_decode($body) : $body);
    }

    /**
     * @return bool
     */
    public function isBase64Encoded(): bool
    {
        return $this->payload['isBase64Encoded'] ?? false;
    }

    /**
     * @return bool
     */
    public function hasMultiValueParameters(): bool
    {
        return array_key_exists('multiValueHeaders', $this->payload);
    }

    /**
     * Retrieve server parameters.
     *
     * Retrieves data related to the incoming request environment,
     * typically derived from PHP's $_SERVER superglobal. The data IS NOT
     * REQUIRED to originate from $_SERVER.
     *
     * @return array
     */
    public function getServerParams()
    {
        return $_ENV;
    }

    /**
     * Retrieve cookies.
     *
     * Retrieves cookies sent by the client to the server.
     *
     * The data MUST be compatible with the structure of the $_COOKIE
     * superglobal.
     *
     * @return array
     */
    public function getCookieParams()
    {
        $cookies = [];
        foreach (explode(';', $this->getHeader('cookie')) as $cookieString) {
            list($name, $value) = explode('=', trim($cookieString));
            $cookies[$name] = $value;
        }

        return $cookies;
    }

    /**
     * Retrieve normalized file upload data.
     *
     * This method returns upload metadata in a normalized tree, with each leaf
     * an instance of Psr\Http\Message\UploadedFileInterface.
     *
     * These values MAY be prepared from $_FILES or the message body during
     * instantiation, or MAY be injected via withUploadedFiles().
     *
     * @return array An array tree of UploadedFileInterface instances; an empty
     *     array MUST be returned if no data is present.
     */
    public function getUploadedFiles()
    {
        return [];
        // TODO: Implement getUploadedFiles() method.
    }

    /**
     * Retrieve any parameters provided in the request body.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, this method MUST
     * return the contents of $_POST.
     *
     * Otherwise, this method may return any results of deserializing
     * the request body content; as parsing returns structured content, the
     * potential types MUST be arrays or objects only. A null value indicates
     * the absence of body content.
     *
     * @return null|array|object The deserialized body parameters, if any.
     *     These will typically be an array or object.
     */
    public function getParsedBody()
    {
        if ($body = (string) $this->getBody()) {
            switch ($this->getHeader('content-type')) {
                case 'application/x-www-form-urlencoded':
                case 'multipart/form-data':
                    if ($this->getMethod() == self::METHOD_POST) {
                        parse_str($body, $query);
                        return $query;
                    }
                    break;

                case 'application/json':
                    return json_decode($body, true);
            }
        }

        return [];
    }

    /**
     * Retrieve attributes derived from the request.
     *
     * The request "attributes" may be used to allow injection of any
     * parameters derived from the request: e.g., the results of path
     * match operations; the results of decrypting cookies; the results of
     * deserializing non-form-encoded message bodies; etc. Attributes
     * will be application and request specific, and CAN be mutable.
     *
     * @return array Attributes derived from the request.
     */
    public function getAttributes()
    {
        return [];
        // TODO: Implement getAttributes() method.
    }

    /**
     * @param string $version
     * @return static
     */
    public function withProtocolVersion($version)
    {
        // TODO: Implement withProtocolVersion() method.
    }

    /**
     * @param string $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     * @return static
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withHeader($name, $value)
    {
        // TODO: Implement withHeader() method.
    }

    /**
     * @param string $name Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     * @return static
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withAddedHeader($name, $value)
    {
        // TODO: Implement withAddedHeader() method.
    }

    /**
     * @param string $name Case-insensitive header field name to remove.
     * @return static
     */
    public function withoutHeader($name)
    {
        // TODO: Implement withoutHeader() method.
    }

    /**
     * @param StreamInterface $body Body.
     * @return static
     * @throws \InvalidArgumentException When the body is not valid.
     */
    public function withBody(StreamInterface $body)
    {
        // TODO: Implement withBody() method.
    }

    /**
     * @link http://tools.ietf.org/html/rfc7230#section-5.3 (for the various
     *     request-target forms allowed in request messages)
     * @param mixed $requestTarget
     * @return static
     */
    public function withRequestTarget($requestTarget)
    {
        // TODO: Implement withRequestTarget() method.
    }

    /**
     * @param string $method Case-sensitive method.
     * @return static
     * @throws \InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod($method)
    {
        // TODO: Implement withMethod() method.
    }

    /**
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @param UriInterface $uri New request URI to use.
     * @param bool $preserveHost Preserve the original state of the Host header.
     * @return static
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        // TODO: Implement withUri() method.
    }

    /**
     * @param array $cookies Array of key/value pairs representing cookies.
     * @return static
     */
    public function withCookieParams(array $cookies)
    {
        // TODO: Implement withCookieParams() method.
    }

    /**
     * @param array $query Array of query string arguments, typically from
     *     $_GET.
     * @return static
     */
    public function withQueryParams(array $query)
    {
        // TODO: Implement withQueryParams() method.
    }

    /**
     * @param array $uploadedFiles An array tree of UploadedFileInterface instances.
     * @return static
     * @throws \InvalidArgumentException if an invalid structure is provided.
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        // TODO: Implement withUploadedFiles() method.
    }

    /**
     * Retrieve a single derived request attribute.
     *
     * Retrieves a single derived request attribute as described in
     * getAttributes(). If the attribute has not been previously set, returns
     * the default value as provided.
     *
     * This method obviates the need for a hasAttribute() method, as it allows
     * specifying a default value to return if the attribute is not found.
     *
     * @see getAttributes()
     * @param string $name The attribute name.
     * @param mixed $default Default value to return if the attribute does not exist.
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        // TODO: Implement getAttribute() method.
    }

    /**
     * @param null|array|object $data The deserialized body data. This will
     *     typically be in an array or object.
     * @return static
     * @throws \InvalidArgumentException if an unsupported argument type is
     *     provided.
     */
    public function withParsedBody($data)
    {
        // TODO: Implement withParsedBody() method.
    }

    /**
     * @param string $name The attribute name.
     * @param mixed $value The value of the attribute.
     * @return static
     */
    public function withAttribute($name, $value)
    {
        // TODO: Implement withAttribute() method.
    }

    /**
     * @param string $name The attribute name.
     * @return static
     */
    public function withoutAttribute($name)
    {
        // TODO: Implement withoutAttribute() method.
    }
}