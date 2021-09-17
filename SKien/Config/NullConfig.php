<?php
declare(strict_types=1);

namespace SKien\Config;

/**
 * Class for empty config.
 *
 * This class can be used to implement the <b>Null object Design Pattern</b>
 * (https://designpatternsphp.readthedocs.io/en/latest/Behavioral/NullObject/README.html).
 *
 * All calls to getXXX() allways returns the passed default value.
 * This class can be used as default instance for any module that uses
 * an Configuration imlementing the ConfigInterface.
 *
 * @package Config
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class NullConfig extends AbstractConfig
{
    /**
     * Allways return the specified default value.
     * @param string $strPath
     * @param mixed $default
     * @return mixed
     */
    public function getValue(string $strPath, $default = null)
    {
        return $default;
    }
}
