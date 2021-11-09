<?php

namespace App\Service;

class InfoPrinter
{
    private int $activeCount;
    private int $pausedCount;
    private int $count;
    private int $startTime;
    private int $peakMemoryUsage;
    private const PRINT__INFO_FREQUENCY = 10000;

    public function initReport(): void
    {
        $this->peakMemoryUsage = memory_get_peak_usage(true);
        $this->startTime = time();
        $this->activeCount = 0;
        $this->pausedCount = 0;
        $this->count = 0;
    }

    public function addCount($isOpen): void
    {
        $this->count++;
        $isOpen ? $this->activeCount++ : $this->pausedCount++;
        $this->printInfo();
    }

    private function convert($size): string
    {
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        return @round($size / (1024 ** ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }

    private function printInfo(): void
    {
        if ($this->count % self::PRINT__INFO_FREQUENCY === 0) {
            $peak = 'Peak memory usage: ' . $this->colorLog(round($this->peakMemoryUsage / 1024 / 1024, 2) . ' mb');
            $memory = 'Current memory usage: ' . $this->colorLog($this->convert(memory_get_usage(true)));
            $active = 'Active: ' . $this->colorLog(number_format($this->activeCount));
            $paused = 'Paused: ' . $this->colorLog(number_format($this->pausedCount));
            $done = 'Total: ' . $this->colorLog(number_format($this->count));
            $elapsed = 'Time: ' . $this->colorLog((time() - $this->startTime) . 's');
            echo sprintf('%s | %s | %s | %s | %s | %s',
                    $peak,
                    $memory,
                    $active,
                    $paused,
                    $done,
                    $elapsed
                ) . PHP_EOL;
        }
    }

    public function colorLog($str, $type = 'i'): string
    {
        switch ($type) {
            case 'e': //error
                return "\033[31m$str \033[0m";
            case 's': //success
                return "\033[32m$str \033[0m";
            case 'w': //warning
                return "\033[33m$str \033[0m";
            case 'i': //info
                return "\033[36m$str \033[0m";
            default:
                return $str;
        }
    }
}