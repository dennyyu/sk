<?php

namespace common\helpers;

/**
 * 专门计算时间相关的Helper函数
 *
 * Dh 代表 Date Helper，为写代码方便，用了缩写，因为用得多，所以短一点
 *
 * @author charles
 */
class Dh
{

    const USE_UNIXTIMESTAMP = true;
    const PERIOD_MINUTIE    = 60;
    const PERIOD_HOUR       = 3600;
    const PERIOD_1DAY       = 86400; //60 * 60 * 24 = 86400
    const PERIOD_30DAYS     = 2592000; //60 * 60 * 24 * 30 = 2592000
    const DATE_OPERATOR_ADD = 'add';
    const DATE_OPERATOR_SUB = 'sub';

    /**
     * @param null $date
     *
     * @return string
     */
    public static function millisecond($date = null)
    {
        return self::second($date) . "1000";
    }

    /**
     * @param $date
     *
     * @return false|int
     */
    public static function second($date)
    {
        if ($date == null) {
            return time();
        }

        return strtotime($date);
    }

    public static function format($time, $format = 'Y-m-d')
    {
        if (!is_int($time)) {
            $time = strtotime($time);
        }

        return date($format, $time);
    }

    public static function date($date = null)
    {
        if (!isset($date)) {
            return date('Y-m-d');
        }

        return self::format($date);
    }

    public static function addDays($interval, $date = null)
    {
        $time = self::second($date);
        $time = $time + self::PERIOD_1DAY * $interval;

        return self::format($time);
    }

    public static function isHoliday($time)
    {
        if (is_string($time)) {
            $time = strtotime($time);
        }
        $week = date('w', $time);

        if ($week == 0
            || $week == 6) {
            return true;
        }

        return false;
    }
}
