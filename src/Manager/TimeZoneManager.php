<?php


namespace App\Manager;


class TimeZoneManager
{
    /**
     * @return string[]
     */
    public function getAll(): array
    {
        return \DateTimeZone::listIdentifiers();
    }
}