<?php
require_once __DIR__ . '/../src/index.php';

use RateLimiter\Target;
use RateLimiter\FileSerializer;
use RateLimiter\SimpleWindowLimiter;

class TestTarget implements Target
{
    private $index;

    public function __construct($index) {
        $this->index = $index;
    }

    public function hashCode(): string
    {
        return "simple_window_target_".$this->index;
    }
}

$serializer = new FileSerializer("./simple_window_log.txt");
// $serializer = new RedisSerializer("simple_window", "tcp://127.0.0.1:6379");
$limiter = new SimpleWindowLimiter($serializer);
// filter logs by a specified key
// $limiter = new SimpleWindowLimiter($serializer, function ($log) {
//     return $log->key === "simple_window_target_1";
// });

$limiter->start();

for ($i = 0; $i < 10; $i++) {
    usleep(250000);
    $now = microtime(true);
    $t = new TestTarget($i % 2);
    if (!$limiter->tryAcquire($t)) {
        echo $now . " " . $t->hashCode() . " limited\n";
    } else {
        echo $now . " " . $t->hashCode() . " do something\n";
    }
}

$limiter->stop();

?>