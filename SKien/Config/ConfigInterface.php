<?php
declare(strict_types=1);

namespace SKien\Config;

/**
 * Interface for config components.
 *
 * @package Config
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
*/
interface ConfigInterface
{
    /**
     * Get the value specified by path.
     * @param string $strPath
     * @param mixed $default
     * @return mixed
     */
    public function getValue(string $strPath, $default = null);

    /**
     * Get the string value specified by path.
     * @param string $strPath
     * @param string $strDefault
     * @return string
     */
    public function getString(string $strPath, string $strDefault = '') : string;

    /**
     * Get the integer value specified by path.
     * @param string $strPath
     * @param int $iDefault
     * @return int
     */
    public function getInt(string $strPath, int $iDefault = 0) : int;

    /**
     * Get the integer value specified by path.
     * @param string $strPath
     * @param float $fltDefault
     * @return float
     */
    public function getFloat(string $strPath, float $fltDefault = 0.0) : float;

    /**
     * Get the boolean value specified by path.
     * @param string $strPath
     * @param bool $bDefault
     * @return bool
     */
    public function getBool(string $strPath, bool $bDefault = false) : bool;

    /**
     * Get the date value specified by path as unix timestamp.
     * @param string $strPath
     * @param int $default default value (unix timestamp)
     * @return int unix timestamp
     */
    public function getDate(string $strPath, int $default = 0) : int;

    /**
     * Get the date and time value specified by path as unix timestamp.
     * @param string $strPath
     * @param int $default default value (unix timestamp)
     * @return int unix timestamp
     */
    public function getDateTime(string $strPath, int $default = 0) : int;

    /**
     * Get the array specified by path.
     * @param string $strPath
     * @param array<mixed> $aDefault
     * @return array<mixed>
     */
    public function getArray(string $strPath, array $aDefault = []) : array;
}
