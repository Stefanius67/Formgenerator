<?php
declare(strict_types=1);

namespace SKien\Config;

/**
 * Class for config component getting data from JSON file.
 *
 * @package Config
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class JSONConfig extends AbstractConfig
{
    /**
     * The constructor expects an valid filename/path to the JSON file.
     * @param string $strConfigFile
     */
    public function __construct(string $strConfigFile)
    {
        $this->aConfig = $this->parseFile($strConfigFile);
    }

    /**
     * Parse the given file an add all settings to the internal configuration.
     * @param string $strConfigFile
     * @return array<mixed>
     */
    protected function parseFile(string $strConfigFile) : array
    {
        if (!file_exists($strConfigFile)) {
            trigger_error('Config File (' . $strConfigFile . ') does not exist!', E_USER_WARNING);
        }

        $aJSON = null;
        $strJson = file_get_contents($strConfigFile);
        if ($strJson !== false) {
            $aJSON = json_decode($strJson, true);
            if ($aJSON === null) {
                trigger_error('Invalid config file (' . $strConfigFile . '): ' . json_last_error_msg(), E_USER_ERROR);
            }
        }
        return $aJSON ?? [];
    }
}
