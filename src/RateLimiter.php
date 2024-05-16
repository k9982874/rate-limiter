<?php

namespace RateLimiter;

interface Target
{
    public function hashCode(): string;
}

class LimitLog {
    private $key;
    private $time;

    public function __construct(string $key, float $time)
    {
        $this->key = $key;
        $this->time = $time;
    }

    public function __get($property)
    {
        if (property_exists($this, $property))
        {
            return $this->$property;
        }
        return null;
    }
}

interface Limiter
{
    public function start();
    public function stop();

    public function tryAcquire(Target $target): bool;
}

interface Serializer
{
    public function serialize(array $limitLogs): bool;
    public function unserialize(): array;
}
