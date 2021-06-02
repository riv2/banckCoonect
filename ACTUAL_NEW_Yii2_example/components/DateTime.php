<?php

namespace app\components;

use DateTimeZone;

/**
 * Класс DateTime
 *
 * @package common\components
 * @property string ofMonth
 */
class DateTime extends \DateTime
{
    const MINUTE = 60;
    const HOUR = 3600;
    const DAY = 86400;
    /** @var bool В каком формате приводить объект к строке (с временем или без) */
    public $withTime = true;

    /** SQL формат строки без времени */
    const DB_DATE_FORMAT = 'Y-m-d';

    /** SQL формат строки с временем */
    const DB_DATETIME_FORMAT = 'Y-m-d H:i:s';

    /** SQL формат строки времени */
    const DB_TIME_FORMAT = 'H:i:s';

    /** SQL IMAP формат строки с временем */
    const IMAP_DATETIME_FORMAT = 'j-M-Y';

    public static $months = [
        1 => 'Январь',
        2 => 'Февраль',
        3 => 'Март',
        4 => 'Апрель',
        5 => 'Май',
        6 => 'Июнь',
        7 => 'Июль',
        8 => 'Август',
        9 => 'Сентябрь',
        10 => 'Октябрь',
        11 => 'Ноябрь',
        12 => 'Декабрь'
    ];

    public static $of_month = [
        1 => 'января',
        2 => 'февраля',
        3 => 'марта',
        4 => 'апреля',
        5 => 'мая',
        6 => 'июня',
        7 => 'июля',
        8 => 'августа',
        9 => 'сентября',
        10 => 'октября',
        11 => 'ноября',
        12 => 'декабря'
    ];

    public static $weekdays = [
        1 => 'Понедельник',
        2 => 'Вторник',
        3 => 'Среда',
        4 => 'Четверг',
        5 => 'Пятница',
        6 => 'Суббота',
        7 => 'Воскресенье'
    ];
    public static $weekdaysShort = [
        1 => 'пн',
        2 => 'вт',
        3 => 'ср',
        4 => 'чт',
        5 => 'пт',
        6 => 'сб',
        7 => 'вс'
    ];

    public static function localizedDateRange( $date1,  $date2) {
        if ($date2) {
            if(!$date1) {
                return ' - ' . $date2->localized();
            }
            if ($date1->format('Ymd') == $date2->format('Ymd')) {
                return $date1->localized() . ' - ' . $date2->format(self::DB_TIME_FORMAT);
            } else {
                return $date1->localized() . ' - ' . $date2->localized();
            }
        } else {
            if(!$date1) {
                return null;
            }
            return $date1->localized();
        }
    }

    /**
     * Конструктор класса
     * @param string       $time
     * @param bool         $withTime
     * @param DateTimeZone $timezone
     */
    public function __construct($time = 'now', $withTime = true, DateTimeZone $timezone = null)
    {
        parent::__construct($time, $timezone);
        $this->withTime = $withTime;
    }

    /**
     * Магическая функция привидения объекта к строковому представлению
     * @return string
     */
    public function __toString()
    {
        return (string)$this->format($this->withTime ? self::DB_DATETIME_FORMAT : self::DB_DATE_FORMAT);
    }

    public function localized() {
        $str = $this->format("d").' '.$this->getOfMonth().' '.$this->format('Y');
        if ($this->withTime) {
            $str .= ' '.$this->format(self::DB_TIME_FORMAT);
        }
        return $str;
    }

    public function getRuMonthOf() {
        return static::$of_month[intval($this->format('m'),10)];
    }

    public static function nump($number, $opts = ['год', 'года', 'лет']) {
        if($number % 100 > 10 && $number % 100 < 20) {
            return $opts[2];
        }
        switch ($number % 10) {
            case 1:
                return $opts[0];
            case 2:
            case 3:
            case 4:
                return $opts[1];
            default:
        }
        return $opts[2];
    }

    public function getOfMonth() {
        return static::$of_month[intval($this->format('m'),10)];
    }

    public static function timeFormatToSeconds($time) {
        $parts = explode(':',$time);
        return intval($parts[0]) * self::HOUR + intval($parts[1]) * self::MINUTE + intval($parts[2]);
    }

    public static function secondsToLabel($seconds, $cutOffSec = false) {
        $timeLbl = '';
        $dtF = new DateTime("@0");
        $dtT = new DateTime("@$seconds");
        $diff = $dtF->diff($dtT);

        $hour = 0;
        if ($diff->d) {
            $hour = $diff->d * 24;
            //$timeLbl .= $diff->d.' д. ';
        }
        if ($diff->h) {
            $hour  += $diff->h;
            // $timeLbl .= $diff->h.' ч. ';
        }
        if ($hour) {
            $timeLbl .= $hour.' ч. ';
        }
        if ($diff->i) {
            $timeLbl .= $diff->i.' мин. ';
        }
        if (!$timeLbl || !$cutOffSec || (!$diff->d && !$diff->h)) {
            if ($diff->s) {
                $timeLbl .= $diff->s . ' сек. ';
            }
        }
        return $timeLbl;
    }
}