# Rate Limiter

A PHP traffic throttling library.

![uml](https://raw.githubusercontent.com/k9982874/rate-limiter/main/rate-limiter.plantuml.png)

## Installation

Add repository in `composer.json`

```
{
      "type": "package",
      "package": {
        "name": "k9982874/rate-limiter",
        "version": "1.0",
        "source": {
          "url": "https://github.com/k9982874/rate-limiter.git",
          "type": "git",
          "reference": "main"
        }
      }
    }
```

Install the latest version with

```bash
$ composer require k9982874/rate-limiter
```

## Basic Usage

Start RateLimiter as soon as pos`sible in your codebase.

```php
<?php

use RateLimiter\SimpleWindowLimiter;

$serializer = new FileSerializer("./simple_window_log.txt");
$limiter = new SimpleWindowLimiter($serializer);

$limiter->start();
```

## Serialization

RateLimiter provides two kind of serializers.
```php
// File Serializer
use RateLimiter\FileSerializer;
$serializer = new FileSerializer("./simple_window_log.txt");

// Redis Serializer
use RateLimiter\RedisSerializer;
$serializer = new RedisSerializer("simple_window", "tcp://127.0.0.1:6379");
```

### Author

Kevin Zhou<k9982874@gmail.com>

### License

RateLimiter is licensed under the GPLv3 License - https://www.gnu.org/licenses/gpl-3.0.en.html