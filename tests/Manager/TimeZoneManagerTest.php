<?php


namespace App\Tests\Manager;


use App\Entity\User;
use App\Manager\TimeZoneManager;
use PHPUnit\Framework\TestCase;

class TimeZoneManagerTest extends TestCase
{
    public function testGetFirstDayOfWeek()
    {
        $tzManager = new TimeZoneManager();

        $frUser = new User();
        $frUser->setTimeZone('Europe/Paris');
        $frFirstDate = $tzManager->getFirstDayOfWeek('2020-11-13 12:00:00', $frUser);

        $this->assertEquals('2020-11-09 00:00:00', $frFirstDate->format('Y-m-d H:i:s'));

        $jpUser = new User();
        $jpUser->setTimeZone('Asia/Tokyo');
        $jpFirstDate = $tzManager->getFirstDayOfWeek('2020-11-13 12:00:00', $jpUser);

        $this->assertEquals('2020-11-08 00:00:00', $jpFirstDate->format('Y-m-d H:i:s'));
    }

    public function testGetLastDayOfWeek()
    {
        $tzManager = new TimeZoneManager();

        $frUser = new User();
        $frUser->setTimeZone('Europe/Paris');
        $frDate = $tzManager->createLocalizedDate('2020-11-13 12:00:00');
        $frFirstDate = $tzManager->getLastDayOfWeek($frDate, $frUser);

        $this->assertEquals('2020-11-15 23:59:59', $frFirstDate->format('Y-m-d H:i:s'));

        $jpUser = new User();
        $jpUser->setTimeZone('Asia/Tokyo');
        $jpDate = $tzManager->createLocalizedDate('2020-11-13 12:00:00');
        $jpFirstDate = $tzManager->getLastDayOfWeek($jpDate, $jpUser);

        $this->assertEquals('2020-11-14 23:59:59', $jpFirstDate->format('Y-m-d H:i:s'));
    }

}
