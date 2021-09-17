<?php
declare(strict_types=1);

namespace SKien\Config;

/**
 * Abstract base class for config components.
 *
 * @package Config
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
abstract class AbstractConfig implements ConfigInterface
{
    /** @var array<mixed> holding the config data    */
    protected ?array $aConfig = null;
    /** @var string format for date parameters (default: 'Y-m-d')    */
    protected string $strDateFormat = 'Y-m-d';
    /** @var string format for datetime parameters (default: 'Y-m-d H:i')    */
    protected string $strDateTimeFormat = 'Y-m-d H:i';
    /** @var string the path separator (default: '.')    */
    protected string $strSeparator = '.';

    /**
     * Set the format for date parameters.
     * See the formatting options DateTime::createFromFormat.
     * In most cases, the same letters as for the date() can be used.
     * @param string $strFormat
     * @link https://www.php.net/manual/en/datetime.createfromformat.php
     */
    public function setDateFormat(string $strFormat) : void
    {
        $this->strDateFormat = $strFormat;
    }

    /**
     * Set the format for datetime parameters.
     * See the formatting options DateTime::createFromFormat.
     * In most cases, the same letters as for the date() can be used.
     * @param string $strFormat
     * @link https://www.php.net/manual/en/datetime.createfromformat.php
     */
    public function setDateTimeFormat(string $strFormat) : void
    {
        $this->strDateTimeFormat = $strFormat;
    }

    /**
     * Set the separator character.
     * Default the '.' is used as separator.
     * @param string $strSeparator
     */
    public function setPathSeparator(string $strSeparator) : void
    {
        $this->strSeparator = $strSeparator;
    }

    /**
     * Get the value specified by path.
     * The path addresses a value within the configuration, which can be nested at
     * any depth (depending on the file format).
     * The individual levels are specified separated by a separator (default is '.').
     * The separator can be changed with `setPathSeparator ()`.
     * @see setPathSeparator()
     * @param string $strPath   the path to the value.
     * @param mixed $default    a default value that is returned if entry doesn't exist
     * @return mixed
     */
    public function getValue(string $strPath, $default = null)
    {
        if ($this->aConfig === null) {
            // without valid config file just return the default value
            return $default;
        }
        $aPath = $this->splitPath($strPath);
        $iDepth = count($aPath);
        $value = null;
        $aValues = $this->aConfig;
        for ($i = 0; $i < $iDepth; $i++) {
            if (!is_array($aValues)) {
                $value = null;
                break;
            }
            if (array_key_exists($aPath[$i], $aValues)) {
                $value = $aValues[$aPath[$i]];
                if ($value === null) {
                    // value exist but is set to null - force to empty string otherwise it would be changed to the default value!
                    $value = '';
                }
            } else {
                $value = null;
                break;
            }
            $aValues = $value;
        }
        return $value ?? $default;
    }

    /**
     * Get the string value specified by path.
     * @param string $strPath
     * @param string $strDefault
     * @return string
     */
    public function getString(string $strPath, string $strDefault = '') : string
    {
        return (string)$this->getValue($strPath, $strDefault);
    }

    /**
     * Get the integer value specified by path.
     * @param string $strPath
     * @param int $iDefault
     * @return int
     */
    public function getInt(string $strPath, int $iDefault = 0) : int
    {
        return intval($this->getValue($strPath, $iDefault));
    }

    /**
     * Get the float value specified by path.
     * @param string $strPath
     * @param float $fltDefault
     * @return float
     */
    public function getFloat(string $strPath, float $fltDefault = 0.0) : float
    {
        return floatval($this->getValue($strPath, $fltDefault));
    }

    /**
     * Get the boolean value specified by path.
     * @param string $strPath
     * @param bool $bDefault
     * @return bool
     */
    public function getBool(string $strPath, bool $bDefault = false) : bool
    {
        $value = $this->getValue($strPath, $bDefault);
        if (!is_bool($value)) {
            $value = $this->boolFromString((string)$value, $bDefault);
        }
        return $value;
    }

    /**
     * Get the date value specified by path.
     * If the config file contains an integer, it is seen as unix timestamp.
     * If the config file contains the date as string, it is parsed with the internal
     * date format set by `setDateFormat()` (default: 'Y-m-d')
     * @param string $strPath
     * @param int $default default value (unix timestamp)
     * @return int date as unix timestamp
     */
    public function getDate(string $strPath, int $default = 0) : int
    {
        $date = (string)$this->getValue($strPath, $default);
        if (!ctype_digit($date)) {
            $dt = \DateTime::createFromFormat($this->strDateFormat, $date);
            $date = $default;
            if ($dt !== false) {
                $aError = $dt->getLastErrors();
                if ($aError['error_count'] == 0) {
                    $dt->setTime(0, 0);
                    $date = $dt->getTimestamp();
                }
            }
        } else {
            $date = intval($date);
        }
        return $date;
    }

    /**
     * Get the date and time value specified by path as unix timestamp.
     * If the config file contains an integer, it is seen as unix timestamp.
     * If the config file contains the date-time as string, it is parsed with the internal
     * date format set by `setDateTimeFormat()` (default: 'Y-m-d H:i')
     * @param string $strPath
     * @param int $default default value (unix timestamp)
     * @return int unix timestamp
     */
    public function getDateTime(string $strPath, int $default = 0) : int
    {
        $date = (string)$this->getValue($strPath, $default);
        if (!ctype_digit($date)) {
            $dt = \DateTime::createFromFormat($this->strDateTimeFormat, $date);
            $date = $default;
            if ($dt !== false) {
                $aError = $dt->getLastErrors();
                if ($aError['error_count'] == 0) {
                    $date = $dt->getTimestamp();
                }
            }
        } else {
            $date = intval($date);
        }
        return $date;
    }

    /**
     * Get the array specified by path.
     * @param string $strPath
     * @param array<mixed> $aDefault
     * @return array<mixed>
     */
    public function getArray(string $strPath, array $aDefault = []) : array
    {
        $value = $this->getValue($strPath, $aDefault);

        // if $value is not an array, the entry exists (otherwise $value = $aDefault, which is an array!),
        // but only contains a single value! In this case we are return an array containing that single element!
        return is_array($value) ? $value : [$value];
    }

    /**
     * Returns the internal array.
     * @return array<mixed>
     */
    public function getConfig() : array
    {
        return $this->aConfig ?? [];
    }

    /**
     * Split the given path in its components.
     * @param string $strPath
     * @return array<string>
     */
    protected function splitPath(string $strPath) : array
    {
        $aSplit = explode($this->strSeparator, $strPath);
        if ($aSplit === false) {
            $aSplit = [$strPath];
        }
        return $aSplit;
    }

    /**
     * Convert string to bool.
     * Accepted values are (case insensitiv): <ul>
     * <li> true, on, yes, 1 </li>
     * <li> false, off, no, none, 0 </li></ul>
     * for all other values the default value is returned!
     * @param string $strValue
     * @param bool $bDefault
     * @return bool
     */
    protected function boolFromString(string $strValue, bool $bDefault = false) : bool
    {
        if ($this->isTrue($strValue)) {
            return true;
        } else if ($this->isFalse($strValue)) {
            return false;
        } else {
            return $bDefault;
        }
    }

    /**
     * Checks whether the passed value is a valid expression for bool 'True'.
     * Accepted values for bool 'true' are (case insensitiv): <i>true, on, yes, 1</i>
     * @param string $strValue
     * @return bool
     */
    protected function isTrue(string $strValue) : bool
    {
        $strValue = strtolower($strValue);
        return in_array($strValue, ['true', 'on', 'yes', '1']);
    }

    /**
     * Checks whether the passed value is a valid expression for bool 'False'.
     * Accepted values for bool 'false' are (case insensitiv): <i>false, off, no, none, 0</i>
     * @param string $strValue
     * @return bool
     */
    protected function isFalse(string $strValue) : bool
    {
        $strValue = strtolower($strValue);
        return in_array($strValue, ['false', 'off', 'no', 'none', '0']);
    }

    /**
     * Merge this instance with values from onather config.
     * Note that the elemenst of the config to merge with has allways higher priority than
     * the elements of this instance. <br/>
     * If both config contains elements with the same key, the value of this instance will be
     * replaced with the value of the config we merge with. <br/>
     * <b>So keep allways the order in wich you merge several configs together in mind.</b>
     * @param AbstractConfig $oMerge
     */
    public function mergeWith(AbstractConfig $oMerge) : void
    {
        $aMerge = $oMerge->getConfig();
        if ($this->aConfig === null) {
            $this->aConfig = $aMerge;
            return;
        }
        $this->aConfig = $this->mergeArrays($this->aConfig, $aMerge);
    }

    /**
     * Merge the values of two array into one resulting array.
     * <b>Note: <i>neither array_merge() nor array_merge_recursive() lead to
     * the desired result</i></b><br/><br/>
     * Assuming following two config:<pre>
     *      $a1 = ["a" => ["c1" => "red", "c2" => "green"]];
     *      $a2 = ["a" => ["c2" => "blue", "c3" => "yellow"]]; </pre>
     * We expect as result for merge($a1, $a2): <pre>
     *      $a3 = ["a" => ["c1" => "red", "c2" => "blue", "c3" => "yellow"]]; </pre>
     * => [a][c1] remains on "red", [a][c2] "green" is replaced by "blue" and [a][c3] is supplemented with "yellow" <br/><br/>
     * But <ol>
     * <li><b>$a3 = array_merge($a1, $a2)</b> will result in: <pre>
     *      $a3 = ["a" => ["c2" => "blue", "c3" => "yellow"]]; </pre>
     * => the entire element [a] is replaced by the content of $a2 - the sub-elements
     * of $a1 that are not contained in $a2 are lost! <br/><br/>
     * </li>
     * <li><b>$a3 = array_merge_recursive($a1, $a2)</b> will result in: <pre>
     *      $a3 = ["a" => ["c1" => red, "c2" => ["green", "blue"], "c3" => "yellow"]]</pre>
     * => [a][c2] changes from string to an array ["green", "blue"]!
     * </li></ol>
     * @param array<mixed> $aBase
     * @param array<mixed> $aMerge
     * @return array<mixed>
     */
    protected function mergeArrays(array $aBase, array $aMerge) : array
    {
        foreach ($aMerge as $keyMerge => $valueMerge) {
            if (isset($aBase[$keyMerge]) && is_array($aBase[$keyMerge]) && is_array($valueMerge)) {
                // The element is available in the basic configuration and both elements contains
                // an array
                // -> call mergeArray () recursively, unless it is a zero index based array in both cases
                if ($this->isAssoc($aBase[$keyMerge]) || $this->isAssoc($valueMerge)) {
                    $aBase[$keyMerge] = $this->mergeArrays($aBase[$keyMerge], $valueMerge);
                    continue;
                }
            }
            // in all other cases either the element from the array that is to be merged is inserted
            // or it has priority over the original element
            $aBase[$keyMerge] = $valueMerge;
        }
        return $aBase;
    }

    /**
     * Check if given array is associative.
     * Only if the array exactly has sequential numeric keys, starting from 0, the
     * array is NOT associative.
     * @param array<mixed> $a
     * @return bool
     */
    protected function isAssoc(array $a) : bool
    {
        if ($a === []) {
            return false;
        }
        return array_keys($a) !== range(0, count($a) - 1);
    }
}