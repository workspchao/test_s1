<?php

namespace Common\Helper;

class DateTimeHelper
{

    public static function fromUnix($Unix)
    {
        try {
            $d = new \DateTime();
            if (is_numeric($Unix)) {
                $Unix = (int) $Unix;
                if (is_long($Unix)) {
                    $d->setTimestamp($Unix);
                    return $d;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function fromString($str)
    {
        try {
            $d = new \DateTime($str);
            return $d;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 
     * @param type $str
     * @param type $format
     * @return \DateTime
     */
    public static function fromFormat($str, $format = 'Y-m-d H:i:s')
    {
        try {
            $d = \DateTime::createFromFormat($format, $str);
            return $d;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getNow()
    {
        try {
            return new \DateTime();
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function toUnix(\DateTime $dt)
    {
        return $dt->getTimestamp();
    }

    public static function toFormat(\DateTime $dt, $format = 'Y-m-d H:i:s')
    {
        return $dt->format($format);
    }
}
