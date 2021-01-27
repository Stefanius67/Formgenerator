<?php
declare(strict_types=1);

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
    /**
     * Creates input field for time values.
     * @param string $strName
     * @param int $wFlags
     */
    public function __construct(string $strName, int $wFlags = 0) 
    {
        parent::__construct($strName, 10, $wFlags);
        $this->strValidate = 'aTime';
    }
    
    /**
     * Accept date value from Formgenerator-data as <ul>
     * <li> DateTime - object </li>
     * <li> unix timestamp (int) </li>
     * <li> English textual datetime description readable by <b>strtotime</b> <br/>
     *      can be a DATE, DATETIME or TIMESTAMP value from a DB query
     * </li></ul>
     * The displayed format can be configured with the <i>'FormTime.Format'</i> parameter
     * as strftime-compatible format string (default settings: '%H:%M) 
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::buildValue()
     * @link https://www.php.net/manual/en/function.strftime.php
     */
    protected function buildValue() : string
    {
        $date = $this->oFG->getData()->getValue($this->strName);
        $strDateFormat = $this->oFG->getConfig()->getString('Time.Format', '%H:%M');
        
        $strValue = '';
        if (is_object($date) && get_class($date) == 'DateTime') {
            // DateTime-object
            $strValue = strftime($strDateFormat, $date->getTimestamp());
        } else if (is_numeric($date)) {
            $strValue = strftime($strDateFormat, $date);
        } else {
            $unixtime = strtotime($date);
            if ($unixtime !== false) {
                $strValue = strftime($strDateFormat, $unixtime);
            }
        }
        
        $strHTML = '';
        if (!$this->oFlags->isSet(FormFlags::NO_ZERO) || ($strValue != 0 && $strValue != '0')) {
            $strHTML = ' value="' . str_replace('"', '&quot;', $strValue) . '"';
        }
        return $strHTML;
    }
}
