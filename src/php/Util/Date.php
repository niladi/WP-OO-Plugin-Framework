<?php


namespace WPPluginCore\Util;
defined('ABSPATH') || exit;
use DateTime;
use DateTimeZone;

class Date
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


    public static function getFirstOfCurrentMonth(?DateTimeZone $timezone = null)
    {
        return new DateTime(self::MONTH_FIRST_DAY, $timezone);
    }

    public static function getLastOfCurrentMonth(?DateTimeZone $timezone = null)
    {
        return new DateTime(self::MONTH_LAST_DAY, $timezone);
    }
    public static function getLastDay(?DateTimeZone $timezone = null)
    {
        return new DateTime(self::LAST_DAY, $timezone);
    }

    public static function getFirstDay(?DateTimeZone $timezone = null)
    {
        return new DateTime(self::FIRST_DAY, $timezone);
    }

    public static function createDateTime($time = 'now', ?DateTimeZone $timezone = null)
    {
        return new DateTime($time, $timezone);
    }

    public static function getLastDayOfMonth(int $time, ?DateTimeZone $timezone = null)
    {
        return new DateTime(date('m-t-Y', $time), $timezone);
    }

    public static function getFirstDayOfMonth(int $time, ?DateTimeZone $timezone = null)
    {
        return new DateTime(date('m-1-Y', $time), $timezone);
    }

}
