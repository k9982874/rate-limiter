<?php
namespace RateLimiter;

class TokenBucketLimiter implements Limiter {
    private $rate;

    private $capacity;

    private $startTime;

    private $serializer;

    private $limitLogs = array();

    public function __construct(Serializer $serializer, $rate = 1, $capacity = 100)
    {
        if ($rate < 1)
        {
            $this->rate = 1;
        }
        else
        {
            $this->rate = $rate;
        }

        $this->capacity = $capacity;
        $this->serializer = $serializer;
    }

    public function start()
    {
        $this->startTime = microtime(true);
        $this->limitLogs = $this->serializer->unserialize();

        for ($i = 0; $i < $this->capacity; $i++)
        {
            array_push($this->limitLogs, new LimitLog("token", $this->startTime));
        }
    }

    public function stop()
    {
        $this->serializer->serialize($this->limitLogs);
    }

    public function tryAcquire(Target $target): bool
    {
        if (count($this->limitLogs) < $this->capacity)
        {
            $now = microtime(true);
            $elapsed = $now - $this->startTime;
            if ($elapsed > 1)
            {
                $restoredPreSecond = $this->capacity / $this->rate;

                $restoredNums = ceil($elapsed * $restoredPreSecond);
                if ($restoredNums + count($this->limitLogs) > $this->capacity)
                {
                    $restoredNums = $this->capacity - count($this->limitLogs);
                }

                if ($restoredNums > 0)
                {
                    for ($i = 0; $i < $restoredNums; $i++)
                    {
                        array_push($this->limitLogs, new LimitLog("token", $now));
                    }

                    $this->startTime = microtime(true);
                }
            }
        }

        // echo "Token remining: ".count($this->limitLogs)." ";

        if (count($this->limitLogs) > 0)
        {
            array_shift($this->limitLogs);
            return true;
        }
        return false;
    }
}
