<?php

namespace App\Service;

class ActiveChecker
{
    private ?string $checkTime = null;
    private int $todayIndex;
    private int $yesterdayIndex;

    /**
     * @throws \Exception
     */
    public function isOpen(array $openingTimes, $openAt = 'now'): bool
    {
        $checkTime = $this->resolveCheckTime($openAt);

        $open = false;

        if (isset($openingTimes[$this->yesterdayIndex])) {
            $open = $this->isOpenFromYesterday($openingTimes, $this->yesterdayIndex, $checkTime);
        }

        if (!$open && isset($openingTimes[$this->todayIndex])) {
            return $this->isOpenToday($openingTimes, $this->todayIndex, $checkTime);
        }

        return $open;
    }

    /**
     * This is for testing purposes
     */
    public function setCheckTime(string $checkTime): void
    {
        $this->checkTime = $checkTime;
    }

    /**
     * @throws \Exception
     */
    private function isOpenFromYesterday($openingTimes, $yesterdayIndex, $checkTime): bool
    {
        foreach ($openingTimes[$yesterdayIndex] as $range) {
            $opening = $this->createOpeningHoursDateTime($checkTime, $range['opening'])->modify('-1day');
            $closing = $this->createOpeningHoursDateTime($checkTime, $range['closing'])->modify('-1day');
            $this->validateOpeningHours($opening, $closing);
            if ($closing <= $opening) {
                $closing->modify('+1day');
            }
            if ($opening <= $checkTime && $checkTime <= $closing) {
                return true;
            }
        }
        return false;
    }

    /**
     * @throws \Exception
     */
    private function isOpenToday($openingTimes, $todayIndex, $checkTime): bool
    {
        foreach ($openingTimes[$todayIndex] as $range) {
            $opening = $this->createOpeningHoursDateTime($checkTime, $range['opening']);
            $closing = $this->createOpeningHoursDateTime($checkTime, $range['closing']);
            $this->validateOpeningHours($opening, $closing);
            if ($closing <= $opening) {
                if ($this->isClosingAndCheckAtMidnight($checkTime, $closing)) {
                    $checkTime->modify('+1day');
                }
                $closing->modify('+1day');
            }
            if ($opening <= $checkTime && $checkTime <= $closing) {
                return true;
            }
        }
        return false;
    }

    private function createOpeningHoursDateTime(\DateTimeInterface $checkTime, $openingHour): \DateTimeInterface
    {
        return \Datetime::createFromFormat('H:i', $openingHour, new \DateTimeZone('UTC'))
            ->setDate(
                $checkTime->format('Y'),
                $checkTime->format('m'),
                $checkTime->format('d')
            );
    }

    /**
     * @throws \Exception
     */
    private function validateOpeningHours($opening, $closing): void
    {
        if (!$opening || !$closing) {
            throw new \Exception(sprintf(
                'Invalid opening hours %s:%s',
                $opening,
                $closing));
        }
    }

    private function isClosingAndCheckAtMidnight($checkTime, $closing): bool
    {
        return intval($closing->format('H')) === 0 &&
            intval($closing->format('i')) === 0 &&
            intval($checkTime->format('H')) === 0 &&
            intval($checkTime->format('i')) === 0;
    }

    private function createDateAsUTC(string $dateTime): \DateTimeInterface
    {
        return (new \DateTime($dateTime, new \DateTimeZone('UTC')));
    }

    private function resolveCheckTime(string $dateTime): \DateTimeInterface
    {
        $checkTime = new \DateTime($dateTime);
        if (null !== $this->checkTime) {
            $checkTime = $this->createDateAsUTC($this->checkTime);
        }
        $this->todayIndex = intval((new \DateTime($checkTime->format('c')))->format('N'));
        $this->yesterdayIndex = intval((new \DateTime($checkTime->format('c')))->modify('-1day')->format('N'));
        return $checkTime;
    }
}