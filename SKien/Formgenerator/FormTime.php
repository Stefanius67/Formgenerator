<?php
namespace SKien\Formgenerator;

/**
 * Input field for time value.
 * - size always 10
 * - field will be added to JS form validation
 *
 * #### History
 * - *2020-05-12*   initial version
 * - *2021-01-07*   PHP 7.4
 *
 * @package Formgenerator
 * @version 1.1.0
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormTime extends FormInput
{
    use GetFromSQL;
    
    /**
     * Creates input field for time values.
     * @param string $strName
     * @param string $strValue
     * @param int $wFlags
     */
    public function __construct(string $strName, string $strValue, int $wFlags = 0) 
    {
        if (strlen($strValue) > 8) {
            $strValue = $this->tsFromSQL('H:i', $strValue);
        } else {
            $strValue = $this->timeFromSQL('H:i', $strValue);
        }
        ($wFlags & self::HIDDEN) == 0 ? $iSize = 10 : $iSize = -1;
        parent::__construct($strName, $strValue, $iSize, $wFlags);
        $this->strValidate = 'aTime';
    }
}
