<?php
declare(strict_types=1);

namespace SKien\Config;

/**
 * Class for empty config.
 * All calls to getXXX() allways returns the passed default value.
 * This class can be used as default instance for any module that uses
 * an Configuration imlementing the ConfigInterface.
 *
 * #### History
 * - *2021-01-01*   initial version
 *
 * @package SKien/GCalendar
 * @version 1.0.0
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class NullConfig extends AbstractConfig
{
    /**
     * Allways retun the specified default value.
     * @param string $strPath
     * @param mixed $default
     * @return mixed
     */
    public function getValue(string $strPath, $default = null)
    {
        return $default;
    }
}
