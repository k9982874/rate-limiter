<?php
namespace RateLimiter;

class FileSerializer implements Serializer
{
    private $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function serialize(array $logs): bool
    {
        if ($file = fopen($this->filePath, "w"))
        {
            foreach ($logs as $item)
            {
                fwrite($file, "$item->key,$item->time\n");
            }
            fclose($file);
            return true;
        }
        return false;
    }

    public function unserialize(): array
    {
        $logs = array();
        if ($file = fopen($this->filePath, "r"))
        {
            while (!feof($file))
            {
                $line = fgets($file);
                if (strlen($line) > 0)
                {
                    $pieces = explode(",", $line);
                    $pieces[1] = floatval($pieces[1]);
                    array_push($logs, new LimitLog($pieces[0], $pieces[1]));
                }
            }
            fclose($file);
        }
        return $logs;
    }
}