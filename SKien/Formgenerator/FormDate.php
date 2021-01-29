<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Input field for date value.
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
class FormDate extends FormInput
{
    /** @var string strftime-compatible format for the date     */
    protected string $strDateFormat = '';
    
    /**
     * Creates input field for date values.
     * @param string $strName
     * @param int $wFlags
     */
    public function __construct(string $strName, int $wFlags = 0) 
    {
        parent::__construct($strName, '10', $wFlags);
    }
    
    /**
     * get date format from configuration (default: '%Y-%m-%d').
     */
    protected function onParentSet() : void
    {
        $strFormat = strtoupper($this->oFG->getConfig()->getString('Date.Format', 'YMD'));
        $strSep = $this->oFG->getConfig()->getString('Date.Separator', '-');
        $aFormat = ['YMD' => '%Y-%m-%d', 'DMY' => '%d-%m-%Y', 'MDY' => '%m-%d-%Y'];
        $this->strDateFormat = $aFormat[$strFormat] ?? '%Y-%m-%d';
        if ($strSep !== '-') {
            $this->strDateFormat = str_replace('-', $strSep, $this->strDateFormat);
        }
        $this->addAttribute('data-validation', 'date:' . $strSep . $strFormat);
    }
    
    /**
     * Accept date value from Formgenerator-data as <ul>
     * <li> DateTime - object </li>
     * <li> unix timestamp (int) </li>
     * <li> English textual datetime description readable by <b>strtotime</b> <br/>
     *      can be a DATE, DATETIME or TIMESTAMP value from a DB query
     * </li></ul>
     * The displayed format can be configured with the <i>'FormDate.Format'</i> parameter
     * as strftime-compatible format string (default settings: '%Y-%m-%d') 
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::buildValue()
     * @link https://www.php.net/manual/en/function.strftime.php  
     */
    protected function buildValue() : string
    {
        $date = $this->oFG->getData()->getValue($this->strName);
        
        $strValue = '';
        if (is_object($date) && get_class($date) == 'DateTime') {
            // DateTime-object
            $strValue = strftime($this->strDateFormat, $date->getTimestamp());
        } else if (is_numeric($date)) {
            $strValue = strftime($this->strDateFormat, $date);
        } else {
            if ($date != '0000-00-00 00:00:00' && $date != '0000-00-00' && $date != '00:00:00') {
                $unixtime = strtotime($date);
                if ($unixtime !== false) {
                    $strValue = strftime($this->strDateFormat, $unixtime);
                }
            }
        }
        
        $strHTML = '';
        if (!$this->oFlags->isSet(FormFlags::NO_ZERO) || ($strValue != 0 && $strValue != '0')) {
            $strHTML = ' value="' . str_replace('"', '&quot;', $strValue) . '"';
        }
        return $strHTML;
    }
}
