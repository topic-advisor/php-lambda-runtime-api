<?php

namespace TopicAdvisor\Lambda\Log;

use Monolog\Handler\AbstractProcessingHandler;

class MemoryLogHandler extends AbstractProcessingHandler
{
    private $logs = [];

    /**
     * Return all logs
     *
     * @return array
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * Clear the logs from memory
     */
    public function clear()
    {
        $this->logs = [];
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     * @return void
     */
    public function write(array $record)
    {
        $this->logs[] = $record['formatted'];
    }
}