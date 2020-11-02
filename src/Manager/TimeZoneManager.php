<?php


namespace App\Manager;


use App\Entity\User;

class TimeZoneManager
{
    /**
     * @return string[]
     */
    public function getAll(): array
    {
        return \DateTimeZone::listIdentifiers();
    }

    /**
     * @param string $time
     * @param User|null $user
     * @return \DateTime
     * @throws \Exception
     */
    public function createDate($time = 'now', ?User $user = null): \DateTime
    {
        $timezone = null;
        if ($user !== null) {
            $timezone = new \DateTimeZone($user->getTimeZone());
        }
        return new \DateTime($time, $timezone);
    }
}