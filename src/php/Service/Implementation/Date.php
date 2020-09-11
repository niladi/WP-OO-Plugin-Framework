<?php


namespace WPPluginCore\Service\Implementation;
defined('ABSPATH') || exit;
use DateTime;
use PhpOffice\PhpSpreadsheet\Shared\TimeZone;
use WPPluginCore\Service\Abstraction\Service;

class Date extends Service
{
    public const DATE_MONTH = 'Y-m';
    public const DATE_DAY = 'Y-m-d';

    public const LAST_DAY = '12/31/2099';
    public const FIRST_DAY = '01/01/2000';
    public const MONTH_FIRST_DAY = 'first day of this month';
    public const MONTH_LAST_DAY = 'last day of this month';

    public const HOUR_IN_SECONDS = 60 * 60;
    public const DAY_IN_SECONDS = 24 * self::HOUR_IN_SECONDS;
    public const WEEK_IN_SECONDS = 4 *self::DAY_IN_SECONDS;
    public const MONTH_IN_SECONDS = 30 * self::DAY_IN_SECONDS;

    public const TIMEZONE = 'Europe/Berlin';

    private $lastDay;
    private $firsDay;
    private $monthFirstDay;
    private $monthLastDay;
    private $timezone;





    protected function __construct()
    {
        parent::__construct();
        $this->timezone = new \DateTimeZone(self::TIMEZONE);
        $this->lastDay = new DateTime(self::LAST_DAY, $this->timezone);
        $this->firsDAy = new DateTime(self::FIRST_DAY, $this->timezone);
        $this->monthFirstDay = new DateTime(self::MONTH_FIRST_DAY, $this->timezone);
        $this->monthLastDay = new DateTime(self::MONTH_LAST_DAY, $this->timezone);
    }

    public function getFirstOfCurrentMonth()
    {
        return $this->monthFirstDay;
    }

    public function getLastOfCurrentMonth()
    {
        return $this->monthLastDay;
    }
    public function getLastDay()
    {
        return $this->lastDay;
    }

    public function getFirstDay()
    {
        return $this->firsDay;
    }

    public function createDateTime($time = 'now')
    {
        return new DateTime($time, $this->timezone);
    }

    public function getLastDayOfMonth(int $time)
    {
        return new DateTime(date('m-t-Y', $time));
    }

    public function getFirstDayOfMonth(int $time)
    {
        return new DateTime(date('m-1-Y', $time));
    }



}
