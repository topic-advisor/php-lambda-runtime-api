<?php

namespace TopicAdvisor\Lambda\RuntimeApi\Util;

class LambdaDumper
{
    public static function dump($data)
    {
        file_put_contents('php://stdout', json_encode($data) ."\n");
    }
}