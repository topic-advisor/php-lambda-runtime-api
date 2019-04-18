<?php

namespace TopicAdvisor\Lambda\RuntimeApi\Http;

use Psr\Http\Message\StreamInterface;
use TopicAdvisor\Lambda\RuntimeApi\InvocationResponse;
use TopicAdvisor\Lambda\RuntimeApi\Http\Psr7\Stream;

class HttpResponse extends InvocationResponse implements HttpResponseInterface
{
    /**
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion()
    {
        // TODO: Implement getProtocolVersion() method.
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode(int $statusCode)
    {
        $this->payload['statusCode'] = $statusCode;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->payload['statusCode'] ?? 0;
    }

    /**
     * @return string
     */
    public function getReasonPhrase()
    {
        // TODO: Implement getReasonPhrase() method.
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->payload['multiValueHeaders'] = [];
        $this->payload['headers'] = [];
        foreach ($headers as $name => $value) {
            if (is_array($value)) {
                $this->payload['multiValueHeaders'][$name] = $value;
                $this->payload['headers'][$name] = $value[0] ?? '';
            } else {
                $this->payload['multiValueHeaders'][$name] = [$value];
                $this->payload['headers'][$name] = $value;
            }
        }
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->payload['multiValueHeaders'] ?? [];
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
     * @param string $name
     * @return string[]
     */
    public function getHeader($name)
    {
        $header = $this->getHeaders()['name'] ?? $this->getHeaders()[strtolower($name)] ?? [];
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
     * @param $body
     */
    public function setBody($body)
    {
        $this->payload['body'] = $body;
    }

    /**
     * @return StreamInterface
     */
    public function getBody()
    {
        return new Stream($this->payload['body'] ?? '');
    }

    /**
     * @param bool $isBase64Encoded
     */
    public function setIsBase64Encoded(bool $isBase64Encoded)
    {
        $this->payload['isBase64Encoded'] = $isBase64Encoded;
    }

    /**
     * @return bool
     */
    public function isBase64Encoded(): bool
    {
        return $this->payload['isBase64Encoded'];
    }

    /**
     * @param string $version HTTP protocol version
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
     * @param int $code The 3-digit integer result code to set.
     * @param string $reasonPhrase The reason phrase to use with the
     *     provided status code; if none is provided, implementations MAY
     *     use the defaults as suggested in the HTTP specification.
     * @return static
     * @throws \InvalidArgumentException For invalid status code arguments.
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        // TODO: Implement withStatus() method.
    }
}