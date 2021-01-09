<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Helper Trait for the package to format some edit field contents.
 * 
 * #### History
 * - *2021-01-07*   initial Version from recent FormHelper.php / PHP 7.4
 *
 * @package Formgenerator
 * @version 1.1.0
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
*/
trait GetFromSQL
{
    /**
     * format date from DB field
     * 
     * @link https://www.php.net/manual/en/function.date.php
     * 
     * @param string $strFormat
     * @param string $date
     * @return string
     */
    protected function dateFromSQL($strFormat, $date) {
        $result = '';
        if (strlen($date) == 10 && $date != '0000-00-00') {
            $Y = intval(substr($date, 0, 4));
            $M = intval(substr($date, 5, 2));
            $D = intval(substr($date, 8, 2));
    
            $time = mktime(0, 0, 0, $M, $D, $Y);
                    
            // TODO: set right locale to format date
            // $result = DTHelper::translateDT(date($strFormat, $time));
            $result = date($strFormat, $time);
        }
        return $result;
    }
    
    /**
     * format time from DB field
     * milliseconds are ignored!
     *
     * @link https://www.php.net/manual/en/function.date.php
     *
     * @param string $strFormat
     * @param string $time
     * @return string
     */
    protected function timeFromSQL($strFormat, $time) {
        $result = '';
        if (strlen($time) == 8) {
            $H = intval(substr($time, 0, 2));
            $i = intval(substr($time, 3, 2));
            // ignore ms...
    
            $t = mktime($H, $i, 0, 0, 0, 0);
    
            $result = date($strFormat, $t);
        }
    
        return $result;
    }
    
    /**
     * format timestamp from DB field
     * 
     * @link https://www.php.net/manual/en/function.date.php
     * 
     * @param string $strFormat
     * @param string $ts
     * @return string
     */
    protected function tsFromSQL($strFormat, $ts) {
        $result = '';
        if (strlen($ts) == 19 && $ts != '0000-00-00 00:00:00') {
            $Y = intval(substr($ts, 0, 4));
            $M = intval(substr($ts, 5, 2));
            $D = intval(substr($ts, 8, 2));
            $h = intval(substr($ts, 11, 2));
            $m = intval(substr($ts, 14, 2));
            $s = intval(substr($ts, 17, 2));
    
            $time = mktime($h, $m, $s, $M, $D, $Y);
            // TODO: set right locale to format date
            // $result = DTHelper::translateDT(date($strFormat, $time));
            $result = date($strFormat, $time);
        }
        return $result;
    }
    
    /**
     * format currency from DB field
     *
     * @param string $strCur
     * @param bool $bCurSymbol   &euro; is appended (default: true)
     * @return string
     */
    protected function curFromSQL(string $strCur, bool $bCurSymbol = true) : string 
    {
        $strOut = number_format(floatval($strCur), 2, ',', '.');
        if ($bCurSymbol) {
            $strOut .= ' &euro;';
        }
        return $strOut;
    }
}
