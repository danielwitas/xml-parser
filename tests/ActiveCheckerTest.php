<?php declare(strict_types=1);

use App\Service\ActiveChecker;
use PHPUnit\Framework\TestCase;

class ActiveCheckerTest extends TestCase
{
    private function getOpeningTimes(): array
    {
        return [
            1 => [
                [
                    'opening' => '10:00',
                    'closing' => '22:00',
                ]
            ],
            2 => [
                [
                    'opening' => '10:00',
                    'closing' => '03:00',
                ]
            ],
            3 => [
                [
                    'opening' => '10:00',
                    'closing' => '22:00',
                ]
            ],
            4 => [
                [
                    'opening' => '14:00',
                    'closing' => '00:00',
                ]
            ],
            5 => [
                [
                    'opening' => '00:00',
                    'closing' => '00:00',
                ]
            ],
            6 => [
                [
                    'opening' => '00:00',
                    'closing' => '10:00',
                ]
            ],
            7 => []
        ];
    }

    public function testIsOpen(): void
    {
        $activeChecker = new ActiveChecker();
        // monday standard case
        $activeChecker->setCheckTime('08-11-2021 09:59');
        $this->assertEquals(false, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('08-11-2021 10:00');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('08-11-2021 21:59');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('08-11-2021 22:00');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('08-11-2021 22:01');
        $this->assertEquals(false, $activeChecker->isOpen($this->getOpeningTimes()));

        // tuesday opens late case
        $activeChecker->setCheckTime('09-11-2021 09:59');
        $this->assertEquals(false, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('09-11-2021 10:00');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('09-11-2021 21:59');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime( '09-11-2021 22:00');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('09-11-2021 22:59');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('09-11-2021 23:59');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('09-11-2021 00:00');
        $this->assertEquals(false, $activeChecker->isOpen($this->getOpeningTimes()));

        // wednesday opens after midnight case
        $activeChecker->setCheckTime('10-11-2021 02:59');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('10-11-2021 03:00');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('10-11-2021 03:01');
        $this->assertEquals(false, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('10-11-2021 09:59');
        $this->assertEquals(false, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('10-11-2021 10:00');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('10-11-2021 10:01');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('10-11-2021 21:59');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('10-11-2021 22:00');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('10-11-2021 22:01');
        $this->assertEquals(false, $activeChecker->isOpen($this->getOpeningTimes()));

        // thursday closes at midnight case
        $activeChecker->setCheckTime('11-11-2021 13:59');
        $this->assertEquals(false, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('11-11-2021 14:00');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('11-11-2021 14:01');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('11-11-2021 23:59');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('11-11-2021 00:00');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('11-11-2021 00:01');

        $this->assertEquals(false, $activeChecker->isOpen($this->getOpeningTimes()));
        // friday opens at midnight closes at midnight case
        $activeChecker->setCheckTime('12-11-2021 02:59');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('12-11-2021 03:00');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('12-11-2021 03:01');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('12-11-2021 09:59');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime( '12-11-2021 10:00');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('12-11-2021 10:01');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('12-11-2021 21:59');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('12-11-2021 22:00');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('12-11-2021 22:01');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime( '12-11-2021 23:59');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('12-11-2021 00:00');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));

        // opens at midnight case
        $activeChecker->setCheckTime( '13-11-2021 00:00');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime( '13-11-2021 00:01');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime( '13-11-2021 09:59');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime( '13-11-2021 10:00');
        $this->assertEquals(true, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime( '13-11-2021 10:01');
        $this->assertEquals(false, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime( '13-11-2021 23:59');
        $this->assertEquals(false, $activeChecker->isOpen($this->getOpeningTimes()));

        // sunday closed whole day case
        $activeChecker->setCheckTime('14-11-2021 02:59');
        $this->assertEquals(false, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime( '14-11-2021 03:00');
        $this->assertEquals(false, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('14-11-2021 03:01');
        $this->assertEquals(false, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime( '14-11-2021 09:59');
        $this->assertEquals(false, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime( '14-11-2021 10:00');
        $this->assertEquals(false, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime( '14-11-2021 10:01');
        $this->assertEquals(false, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime( '14-11-2021 21:59');
        $this->assertEquals(false, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime( '14-11-2021 22:00');
        $this->assertEquals(false, $activeChecker->isOpen($this->getOpeningTimes()));
        $activeChecker->setCheckTime('14-11-2021 22:01');
        $this->assertEquals(false, $activeChecker->isOpen($this->getOpeningTimes()));

    }
}