<?php


namespace App\Manager;


use App\Entity\User;

class TimeZoneManager
{
    const FIRST_DAYS = [
        'DEFAULT' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'AD' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'AE' => [ 'strDay' => 'saturday', 'iDay' => 6 ],
        'AF' => [ 'strDay' => 'saturday', 'iDay' => 6 ],
        'AG' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'AI' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'AL' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'AM' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'AN' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'AR' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'AS' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'AT' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'AU' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'AX' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'AZ' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'BA' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'BD' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'BE' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'BG' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'BH' => [ 'strDay' => 'saturday', 'iDay' => 6 ],
        'BM' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'BN' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'BR' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'BS' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'BT' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'BW' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'BY' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'BZ' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'CA' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'CH' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'CL' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'CM' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'CN' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'CO' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'CR' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'CY' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'CZ' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'DE' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'DJ' => [ 'strDay' => 'saturday', 'iDay' => 6 ],
        'DK' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'DM' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'DO' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'DZ' => [ 'strDay' => 'saturday', 'iDay' => 6 ],
        'EC' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'EE' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'EG' => [ 'strDay' => 'saturday', 'iDay' => 6 ],
        'ES' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'ET' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'FI' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'FJ' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'FO' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'FR' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'GB' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'GE' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'GF' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'GP' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'GR' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'GT' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'GU' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'HK' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'HN' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'HR' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'HU' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'ID' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'IE' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'IL' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'IN' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'IQ' => [ 'strDay' => 'saturday', 'iDay' => 6 ],
        'IR' => [ 'strDay' => 'saturday', 'iDay' => 6 ],
        'IS' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'IT' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'JM' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'JO' => [ 'strDay' => 'saturday', 'iDay' => 6 ],
        'JP' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'KE' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'KG' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'KH' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'KR' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'KW' => [ 'strDay' => 'saturday', 'iDay' => 6 ],
        'KZ' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'LA' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'LB' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'LI' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'LK' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'LT' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'LU' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'LV' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'LY' => [ 'strDay' => 'saturday', 'iDay' => 6 ],
        'MC' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'MD' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'ME' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'MH' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'MK' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'MM' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'MN' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'MO' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'MQ' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'MT' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'MV' => [ 'strDay' => 'friday', 'iDay' => 5 ],
        'MX' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'MY' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'MZ' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'NI' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'NL' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'NO' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'NP' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'NZ' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'OM' => [ 'strDay' => 'saturday', 'iDay' => 6 ],
        'PA' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'PE' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'PH' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'PK' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'PL' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'PR' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'PT' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'PY' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'QA' => [ 'strDay' => 'saturday', 'iDay' => 6 ],
        'RE' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'RO' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'RS' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'RU' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'SA' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'SD' => [ 'strDay' => 'saturday', 'iDay' => 6 ],
        'SE' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'SG' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'SI' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'SK' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'SM' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'SV' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'SY' => [ 'strDay' => 'saturday', 'iDay' => 6 ],
        'TH' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'TJ' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'TM' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'TR' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'TT' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'TW' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'UA' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'UM' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'US' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'UY' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'UZ' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'VA' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'VE' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'VI' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'VN' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'WS' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'XK' => [ 'strDay' => 'monday', 'iDay' => 1 ],
        'YE' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'ZA' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
        'ZW' => [ 'strDay' => 'sunday', 'iDay' => 0 ],
    ];


    /**
     * @return string[]
     */
    public function getAll(): array
    {
        return \DateTimeZone::listIdentifiers();
    }

    /**
     * @param string|\DateTime $time
     * @param User|null $user
     * @return \DateTime
     * @throws \Exception
     */
    public function createLocalizedDate($time = 'now', ?User $user = null): \DateTime
    {
        $timezone = null;
        if ($user !== null) {
            $timezone = new \DateTimeZone($user->getTimeZone());
        }

        if ($time instanceof \DateTime) {
            $output = clone $time;
            $output->setTimezone(new \DateTimeZone($user->getTimeZone()));
            return $output;
        }

        return new \DateTime($time, $timezone);
    }

    /**
     * @param \DateTimeZone $dtz
     * @return array
     */
    private function getFirstDayConf(\DateTimeZone $dtz): array
    {
        $countryCode = strtoupper($dtz->getLocation()['country_code'] ?? 'DEFAULT');
        if (isset(self::FIRST_DAYS[$countryCode])) {
            return self::FIRST_DAYS[$countryCode];
        }
        return self::FIRST_DAYS['DEFAULT'];
    }

    /**
     * @param \DateTime|string $date
     * @param User $user
     * @return \DateTime
     * @throws \Exception
     */
    public function getFirstDayOfWeek($date, User $user): \DateTime
    {
        $firstDayDate = $this->createLocalizedDate($date, $user);
        $firstDayDate->setTime(0, 0, 0);
        $firstDayConf = $this->getFirstDayConf($firstDayDate->getTimezone());

        if ((int)$firstDayDate->format('w') === $firstDayConf['iDay']) {
            return $firstDayDate;
        }

        $firstDayDate->modify('previous ' . $firstDayConf['strDay']);
        return $firstDayDate;
    }

    /**
     * @param \DateTime|string $date
     * @param User $user
     * @return \DateTime
     * @throws \Exception
     */
    public function getLastDayOfWeek($date, User $user): \DateTime
    {
        $firstDay = $this->getFirstDayOfWeek($date, $user);
        $firstDay->add(new \DateInterval('P6D'));
        $firstDay->setTime(23, 59, 59);
        return $firstDay;
    }
}
