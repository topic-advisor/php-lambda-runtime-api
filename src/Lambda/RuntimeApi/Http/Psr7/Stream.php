<?php

namespace TopicAdvisor\Lambda\RuntimeApi\Http\Psr7;

use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
    /** @var string  */
    private $content;
    
    /** @var bool */
    private $eof = true;

    public function __construct($content = '')
    {
        $this->content = $content;
    }

    public function __toString()
    {
        return $this->content;
    }

    public function close()
    {
    }

    public function detach()
    {
    }

    public function getSize()
    {
    }

    public function tell()
    {
        return 0;
    }

    public function eof()
    {
        return $this->eof;
    }

    public function isSeekable()
    {
        return true;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
    }

    public function rewind()
    {
        $this->eof = false;
    }

    public function isWritable()
    {
        return false;
    }

    public function write($string)
    {
    }

    public function isReadable()
    {
        return true;
    }

    public function read($length)
    {
        $this->eof = true;

        return $this->content;
    }

    public function getContents()
    {
        return $this->content;
    }

    public function getMetadata($key = null)
    {
    }
}