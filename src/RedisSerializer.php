<?php
namespace RateLimiter;

use Predis;

class RedisSerializer implements Serializer
{
    private $name;
    private $dsn;

    public function __construct(string $name, string $dsn)
    {
        $this->name = $name;
        $this->dsn = $dsn;
    }

    public function serialize(array $logs): bool
    {
        $client = new Predis\Client($this->dsn);
        $pipe = $client->pipeline();
        foreach ($logs as $item)
        {
            $pipe = $pipe->hset($this->name, $item->key, $item->time);
        }
        $pipe->execute();
        return true;
    }

    public function unserialize(): array
    {
        $logs = array();
        $client = new Predis\Client($this->dsn);
        $map = $client->hgetall($this->name);
        foreach ($map as $key => $value)
        {
            array_push($logs, new LimitLog($key, floatval($value)));
        }
        return $logs;
    }
}