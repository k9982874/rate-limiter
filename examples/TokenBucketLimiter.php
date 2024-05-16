<?php
require_once __DIR__ . '/../src/index.php';

use RateLimiter\Target;
use RateLimiter\FileSerializer;
use RateLimiter\SimpleWindowLimiter;

class TestTarget implements Target
{
    public function hashCode(): string
    {
        return "token_bucket_target";
    }
}

$serializer = new FileSerializer("./token_bucket_log.txt");
// $serializer = new RedisSerializer("token_bucket", "tcp://127.0.0.1:6379");
$limiter = new TokenBucketLimiter($serializer, 3, 50);

$limiter->start();

for ($i = 0; $i < 10; $i++)
{
    usleep(250000);

    $n = rand(5, 15);
    $now = microtime(true);
    for ($j = 0; $j < $n; $j++)
    {
        if (!$limiter->tryAcquire(new TestTarget()))
        {
            echo $now." limited\n";
        }
        else
        {
            echo $now." do something\n";
        }
    }
}

$limiter->stop();
?>