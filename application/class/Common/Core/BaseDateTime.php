<?php

namespace Common\Core;

use Common\Helper\DateTimeHelper;

class BaseDateTime
{

    protected $dtStr;
    protected $dtUnix;
    protected $timezone_format;

    public static function now()
    {
        $dt = DateTimeHelper::getNow();
        $a = new BaseDateTime();
        $a->setDateTimeUnix(DateTimeHelper::toUnix($dt));
        return $a;
    }

    public static function fromUnix($dtUnix)
    {
        $a = new BaseDateTime();
        $a->setDateTimeUnix($dtUnix);
        return $a;
    }

    public static function fromString($dtStr, $format = null)
    {
        $a = new BaseDateTime();
        $a->setDateTimeString($dtStr, $format);
        return $a;
    }

    public function setDateTimeString($dtStr, $format = null)
    {
        if ($format == null) {
            $dt = DateTimeHelper::fromString($dtStr);
        } else {
            $dt = DateTimeHelper::fromFormat($dtStr, $format);
        }

        if ($dt) {
            $this->dtStr = $dtStr;
            $this->dtUnix = DateTimeHelper::toUnix($dt);
        }
    }

    public function setDateTimeUnix($dtUnix)
    {
        if ($dt = DateTimeHelper::fromUnix($dtUnix)) {
            $this->dtUnix = $dtUnix;
            $this->dtStr = DateTimeHelper::toFormat($dt);
        }
    }

    /*
     * This class considered null if either str/unix is null
     */
    public function isNull()
    {
        return ($this->getUnix() === NULL or $this->getString() === NULL);
    }

    public function getString()
    {
        return $this->getFormat();  //always output as standard format
    }

    public function getFormat($format = 'Y-m-d H:i:s')
    {
        if ($this->getDateTime() instanceof \DateTime) {
            return DateTimeHelper::toFormat($this->getDateTime(), $format);
        }

        return null;
    }

    public function getUnix()
    {
        return $this->dtUnix;
    }

    public function getTimeString()
    {
        return $this->getFormat('H:i:s');
    }

    public function getDateTime()
    {
        return DateTimeHelper::fromUnix($this->getUnix());
    }

    public function addSecond($second)
    {
        $dt = $this->getDateTime()->add(new \DateInterval('PT' . $second . 'S'));
        return $this->_updateByDateTime($dt);
    }

    public function addMinute($min)
    {
        $dt = $this->getDateTime()->add(new \DateInterval('PT' . $min . 'M'));
        return $this->_updateByDateTime($dt);
    }

    public function addHour($hour)
    {
        $dt = $this->getDateTime()->add(new \DateInterval('PT' . $hour . 'H'));
        return $this->_updateByDateTime($dt);
    }

    public function addDay($day)
    {
        $dt = $this->getDateTime()->add(new \DateInterval('P' . $day . 'D'));
        return $this->_updateByDateTime($dt);
    }

    public function addMonth($month)
    {
        $dt = $this->getDateTime()->add(new \DateInterval('P' . $month . 'M'));
        return $this->_updateByDateTime($dt);
    }

    public function addYear($year)
    {
        $dt = $this->getDateTime()->add(new \DateInterval('P' . $year . 'Y'));
        return $this->_updateByDateTime($dt);
    }

    public function subSecond($second)
    {
        $dt = $this->getDateTime()->sub(new \DateInterval('PT' . $second . 'S'));
        return $this->_updateByDateTime($dt);
    }

    public function subMinute($min)
    {
        $dt = $this->getDateTime()->sub(new \DateInterval('PT' . $min . 'M'));
        return $this->_updateByDateTime($dt);
    }

    public function subHour($hour)
    {
        $dt = $this->getDateTime()->sub(new \DateInterval('PT' . $hour . 'H'));
        return $this->_updateByDateTime($dt);
    }

    public function subDay($day)
    {
        $dt = $this->getDateTime()->sub(new \DateInterval('P' . $day . 'D'));
        return $this->_updateByDateTime($dt);
    }

    public function subMonth($month)
    {
        $dt = $this->getDateTime()->sub(new \DateInterval('P' . $month . 'M'));
        return $this->_updateByDateTime($dt);
    }

    public function subYear($year)
    {
        $dt = $this->getDateTime()->sub(new \DateInterval('P' . $year . 'Y'));
        return $this->_updateByDateTime($dt);
    }

    protected function _updateByDateTime(\DateTime $dt)
    {
        $this->dtUnix = DateTimeHelper::toUnix($dt);
        $this->dtStr = DateTimeHelper::toFormat($dt);
        return $this;
    }

    public static function diffFromSystemTime($timeZone)
    {
        $sysDT = BaseDateTime::now()->getDateTime();
        $localDT = new \DateTime('now', new \DateTimeZone($timeZone));

        return $localDT->getOffset() - $sysDT->getOffset();
    }

    public static function getSystemTimeFromLocalTime($localTime, $timeZone)
    {
        $offset = BaseDateTime::diffFromSystemTime($timeZone);
        $local = BaseDateTime::fromString($localTime);
        return BaseDateTime::fromUnix($local->getUnix() - $offset);
    }

    public function getLocalDateTimeStr($display_format = 'd M Y h:i A')
    {
        $dateTimeUTC = new \DateTime($this->dtStr, new \DateTimeZone('UTC'));
        if ($this->timezone_format != NULL && $this->timezone_format != "") {
            $dateTimeUTC->setTimezone(new \DateTimeZone($this->timezone_format));
        }

        return $dateTimeUTC->format($display_format);
    }

    public function setTimeZoneFormat($timezone_format)
    {
        $this->timezone_format = $timezone_format;
    }

    public function getTimeZoneFormat()
    {
        return $this->timezone_format;
    }

    public function jsonSerialize()
    {
        return [
            'datetime' => $this->getString()
        ];
    }
}
