<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Input field for time value.
 * - size always 10
 * - field will be added to JS form validation
 *
 * @package Formgenerator
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormTime extends FormInput
{
    /** @var string strftime-compatible format for the time     */
    protected string $strTimeFormat = '';
    
    /**
     * Creates input field for time values.
     * @param string $strName
     * @param int $wFlags
     */
    public function __construct(string $strName, int $wFlags = 0) 
    {
        parent::__construct($strName, 10, $wFlags);
    }
    
    /**
     * get time format from configuration (default: '%H:%M').
     */
    protected function onParentSet() : void
    {
        $this->setPlaceholder($this->oFG->getConfig()->getString('Time.Placeholder'));
        $bSeconds = $this->oFG->getConfig()->getBool('Time.Seconds', false);
        $strSep = $this->oFG->getConfig()->getString('Time.Separator', ':');
        $this->strTimeFormat = ($bSeconds ? '%H:%M:%S' : '%H:%M');
        if ($strSep !== ':') {
            $this->strTimeFormat = str_replace(':', $strSep, $this->strTimeFormat);
        }
        $this->addAttribute('data-validation', 'time:' . $strSep . ($bSeconds ? '1' : '0') . 'm');
        $this->addPicker($strSep, $bSeconds);
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
        
        $strValue = '';
        if (is_object($date) && get_class($date) == 'DateTime') {
            // DateTime-object
            $strValue = strftime($this->strTimeFormat, $date->getTimestamp());
        } else if (is_numeric($date)) {
            $strValue = strftime($this->strTimeFormat, $date);
        } else {
            $unixtime = strtotime($date);
            if ($unixtime !== false) {
                $strValue = strftime($this->strTimeFormat, $unixtime);
            }
        }
        
        $strHTML = '';
        if (!$this->oFlags->isSet(FormFlags::NO_ZERO) || ($strValue != 0 && $strValue != '0')) {
            $strHTML = ' value="' . str_replace('"', '&quot;', $strValue) . '"';
        }
        return $strHTML;
    }
    
    /**
     * Add attributes for the time picker.
     * @param string $strSep
     * @param bool $bSeconds
     */
    protected function addPicker(string $strSep, bool $bSeconds) : void
    {
        if ($this->oFlags->isSet(FormFlags::ADD_TIME_PICKER)) {
            $this->addAttribute('autocomplete', 'off');
            $strTimePickerFormat = ($bSeconds ? 'HH:MM:SS"' : 'HH:MM');
            if ($strSep !== ':') {
                $strTimePickerFormat = str_replace(':', $strSep, $strTimePickerFormat);
            }
            $this->addAttribute('data-picker', 'time:' . $strTimePickerFormat);
            $aDTSel = $this->oFG->getConfig()->getArray('DTSel');
            if (count($aDTSel) > 0) {
                $this->oFG->addConfigForJS('DTSel', $aDTSel);
            }
        }
    }
}
