<?php
namespace SKien\Formgenerator;

/**
 * Input field for date value.
 * - size always 10
 * - field will be added to JS form validation
 * 
 * The use of HTML5 type="date" causes following problems:
 * - width is dependent on browser, ignores requested size and changes (increase) 
 *   sometimes when datepicker was used !!
 * - not supported by every browser so far
 * - have to be set in YYYY-MM-DD and item.value contains date in YYYY-MM-DD
 *    -> internal logic must be changed (set no problem - DB saving is more tricky...)
 *    -> own JS validation have to be disabled...
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
class FormDate extends FormInput
{
    use GetFromSQL;
    
    /**
     * Creates input field for date values.
     * @param string $strName
     * @param string $strValue
     * @param int $wFlags
     */
    public function __construct(string $strName, string $strValue, int $wFlags = 0) 
    {
        // $this->strType = 'date'; see comment in header !!!!
        $strDate = $this->dateFromSQL('d.m.Y', $strValue);
        if ($strDate == '') {
            // can be DATE or DATETIME / TIMESTAMP value...  so try again with 'long' format
            $strDate = $this->tsFromSQL('d.m.Y', $strValue);
        }
        ($wFlags & self::HIDDEN) == 0 ? $iSize = 10 : $iSize = -1;
        parent::__construct($strName, $strDate, $iSize, $wFlags);
        $this->strValidate = 'aDate';
    }
}
