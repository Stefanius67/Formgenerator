<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Input field for date value.
 *
 * Size is fixed to 10 characters.
 * Default the format is tried to get from the local environment, but can be
 * specified in the configuration.
 *
 * The value that is submitted is always a string in the specified date format!
 *
 * @SKienImage FormDate.png
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormDate extends FormInput
{
    /** @var string strftime-compatible format for the date     */
    protected string $strDateFormat = '';

    /**
     * Creates input field for date values.
     * @param string $strName   Name (if no ID specified, name is used also as ID)
     * @param int $wFlags       any combination of FormFlag constants
     */
    public function __construct(string $strName, int $wFlags = 0)
    {
        parent::__construct($strName, '10', $wFlags);
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     * @internal
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        $strName = self::getAttribString($oXMLElement, 'name');
        $wFlags = self::getAttribFlags($oXMLElement);
        $oFormElement = new self($strName, $wFlags);
        $oFormParent->add($oFormElement);
        $oFormElement->readAdditionalXML($oXMLElement);
        return $oFormElement;
    }

    /**
     * get date format from configuration (default: '%Y-%m-%d').
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormInput::onParentSet()
     */
    protected function onParentSet() : void
    {
        $this->setPlaceholder($this->oFG->getConfig()->getString('Date.Placeholder'));
        if ($this->oFG->getConfig()->getBool('Date.UseHTML5Type')) {
            // if the HTML5 date input used
            // - value must be in format YYYY-MM-DD
            // - ui-formating and validation is the job of the browser ;-)
            // TODO: - adjust width in % since type="date" input 'ignores' the size attrib!
            //         and the size differs from browser to browser...
            //       - may check browser => safari didn't support input type="date"
            $this->strType = 'date';
            $this->strDateFormat = '%Y-%m-%d';
            return;
        }
        $strFormat = strtoupper($this->oFG->getConfig()->getString('Date.Format', 'YMD'));
        $strSep = $this->oFG->getConfig()->getString('Date.Separator', '-');
        $aFormat = ['YMD' => '%Y-%m-%d', 'DMY' => '%d-%m-%Y', 'MDY' => '%m-%d-%Y'];
        $this->strDateFormat = $aFormat[$strFormat] ?? '%Y-%m-%d';
        if ($strSep !== '-') {
            $this->strDateFormat = str_replace('-', $strSep, $this->strDateFormat);
        }
        $this->addAttribute('data-validation', 'date:' . $strSep . $strFormat);
        $this->addPicker($strSep, $strFormat);
    }

    /**
     * Accept date value from FormData as <ul>
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
            $strValue = strftime($this->strDateFormat, intval($date));
        } else if ($date != '0000-00-00 00:00:00' && $date != '0000-00-00' && $date != '00:00:00') {
            $unixtime = strtotime($date);
            if ($unixtime !== false) {
                $strValue = strftime($this->strDateFormat, $unixtime);
            }
        }

        $strHTML = '';
        if (!$this->oFlags->isSet(FormFlags::NO_ZERO) || ($strValue != 0 && $strValue != '0')) {
            $strHTML = ' value="' . str_replace('"', '&quot;', $strValue) . '"';
        }
        return $strHTML;
    }

    /**
     * Add attributes for the date picker.
     * @param string $strSep
     * @param string $strFormat
     */
    protected function addPicker(string $strSep, string $strFormat) : void
    {
        if ($this->oFlags->isSet(FormFlags::ADD_DATE_PICKER)) {
            $this->addAttribute('autocomplete', 'off');
            $aFormat = ['YMD' => 'yyyy-mm-dd', 'DMY' => 'dd-mm-yyyy', 'MDY' => 'mm-dd-yyyy'];
            $strDatePickerFormat = $aFormat[$strFormat] ?? 'yyyy-mm-dd';
            if ($strSep !== '-') {
                $strDatePickerFormat = str_replace('-', $strSep, $strDatePickerFormat);
            }
            $this->addAttribute('data-picker', 'date:' . $strDatePickerFormat);
            $aDTSel = $this->oFG->getConfig()->getArray('DTSel');
            if (count($aDTSel) > 0) {
                $this->oFG->addConfigForJS('DTSel', $aDTSel);
            }
        }
    }
}
