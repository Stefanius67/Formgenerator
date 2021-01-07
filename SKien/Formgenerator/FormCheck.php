<?php
namespace SKien\Formgenerator;

/**
 * Checkbox input field.
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
class FormCheck extends FormInput
{
    /**@var bool state of the checkbox  */
    protected bool $bChecked;

    /**
     * Create a checkbox.
     * As default, the value 'on' is submitted for a checked element. This value can be 
     * changed by setting the param $strValue. <br/>
     * <b>Note: HTML does not submit any value for unchecked checkboxes !!!.</b>
     * @param string $strName   name AND id of the element
     * @param bool $bChecked    initial value
     * @param int $wFlags    (default: 0)
     * @param string $strValue  value to submit, if checked  (default: '' -> 'on' is submitted)
     * @param string $strSuffix Text after the checkbox (default: '')
     */
    public function __construct(string $strName, bool $bChecked, int $wFlags = 0, string $strValue='', string $strSuffix='') 
    {
        $this->strName = $strName;
        $this->strValue = $strValue;
        $this->bChecked = $bChecked;
        $this->wFlags = $wFlags;
        $this->strSuffix = $strSuffix;
        if (($this->wFlags & (self::READ_ONLY | self::DISABLED)) != 0) {
            $this->addAttribute('disabled');
        }
        if ($this->bChecked) {
            $this->addAttribute('checked');
        }
    }
    
    /**
     * Build the HTML-markup for the checkbox.
     * @return string
     */
    public function getHTML() : string 
    {
        $strHTML = $this->buildContainerDiv();
        
        $strHTML .= '<input';
        $strHTML .= ' type="checkbox"';
        
        // note: because checkboxes don't support readonly-attribute and disabled checkboxes are not posted to
        // reciever, we insert an hidden field with name and id to keep value 'alive'
        // so we dont set name and id for readonly or disabled checkboxes!
        if (($this->wFlags & (self::READ_ONLY | self::DISABLED)) == 0) {
            $strHTML .= ' name="' . $this->strName . '"';
            $strHTML .= ' id="' . $this->strName . '"';
        }
        $strHTML .= $this->buildStyle();
        $strHTML .= $this->buildAttributes();
        $strHTML .= $this->buildTab($this->iTab);
        $strHTML .= $this->buildValue($this->strValue);

        // see comment beyond for name and id...
        if (($this->wFlags & (self::READ_ONLY | self::DISABLED)) != 0) {
            $strHTML .= '><input';
            $strHTML .= ' type="hidden"';
            $strHTML .= ' name="' . $this->strName . '"';
            $strHTML .= ' id="' . $this->strName . '"';
            ($this->bChecked) ? $strHTML .= ' value="1"' : $strHTML .= ' value="0"' ;
        }
        $strHTML .= '>';
        
        if (!empty($this->strSuffix)) {
            $strHTML .= '&nbsp;' . $this->strSuffix;
        }
        
        $strHTML .= '</div>' . PHP_EOL;

        return $strHTML;
    }
    
    /**
     * Build the markup for the value attribute.
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormInput::buildValue()
     */
    protected function buildValue($strValue) : string
    {
        $strHTML = '';
        if (!empty($strValue)) {
            $strHTML .= ' value="' . $strValue . '"';
        }
        return $strHTML;
    }
}
