<?php
namespace RateLimiter;

class SimpleWindowLimiter implements Limiter
{
    private $qps = 2;
    private $timeWindows = 1000;

    private $startTime;

    private $serializer;
    private $filter;

    private $limitLogs = array();

    public function __construct(Serializer $serializer, ?callable $filter = null, $qps = 2, $timeWindows = 1000)
    {
        $this->qps = $qps;
        $this->timeWindows = $timeWindows;
        $this->serializer = $serializer;
        $this->filter = $filter;
    }

    public function start()
    {
        $this->startTime = microtime(true) * 1000;
        $this->limitLogs = $this->serializer->unserialize();
    }

    public function stop()
    {
        $this->serializer->serialize($this->limitLogs);
    }

    public function tryAcquire(Target $target): bool
    {
        $now = microtime(true) * 1000;
        $elapsed = $now - $this->startTime;
        if ($elapsed > $this->timeWindows)
        {
            $this->limitLogs = array();
            $this->startTime = microtime(true) * 1000;
        }
        array_push($this->limitLogs, new LimitLog($target->hashCode(), $now));

        $items = $this->limitLogs;
        if ($this->filter !== null)
        {
            $items = array_filter($items, $this->filter);
        }
        return count($items) <= $this->qps;
    }
}
