<?php
declare(strict_types=1);

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
    // TODO: check if FormInput base is needed
    /**
     * Create a checkbox.
     * As default, the value 'on' is submitted for a checked element. This value can be 
     * changed by setting a btnValue for the elementname in the FormData. <br/>
     * <b>Note: HTML does not submit any value for unchecked checkboxes !!!.</b>
     * @param string $strName   name AND id of the element
     * @param int $wFlags    (default: 0)
     * @param string $strSuffix Text after the checkbox (default: '')
     */
    public function __construct(string $strName, int $wFlags = 0, string $strSuffix = '') 
    {
        $this->oFlags = new FormFlags($wFlags);
        $this->strName = $strName;
        $this->strSuffix = $strSuffix;
        if ($this->oFlags->isSet(FormFlags::READ_ONLY | FormFlags::DISABLED)) {
            $this->addAttribute('disabled');
        }
    }
    
    /**
     * Build the HTML-markup for the checkbox.
     * @return string
     */
    public function getHTML() : string 
    {
        $this->processFlags();
        $bChecked = $this->oFG->oData->getValue($this->strName);
        if ($bChecked) {
            $this->addAttribute('checked');
        }
        
        $strHTML = $this->buildContainerDiv();
        $strHTML .= '<input type="checkbox"';
        $strHTML .= $this->buildStyle();
        $strHTML .= $this->buildAttributes();
        $strHTML .= $this->buildTab();
        $strHTML .= $this->buildValue();
        
        // NOTE: because checkboxes don't support readonly-attribute and disabled checkboxes 
        // are not posted to reciever, we insert an hidden field with name and id to keep 
        // value 'alive'
        // -> so we dont set name and id for readonly or disabled checkbox!
        if ($this->oFlags->isSet(FormFlags::READ_ONLY | FormFlags::DISABLED)) {
            $strHTML .= '><input';
            $strHTML .= ' type="hidden"';
            $strHTML .= ' name="' . $this->strName . '"';
            $strHTML .= ' id="' . $this->strName . '"';
            $strValue = $this->oFG->oData->getBtnValue($this->strName) ?: 'on';
            $strHTML .= ($bChecked) ? ' value="' . $strValue . '"' : $strHTML .= ' value="off"';
        } else {
            $strHTML .= ' name="' . $this->strName . '"';
            $strHTML .= ' id="' . $this->strName . '"';
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
     * For checkboxes value comes from the getBtnValue().
     * @return string
     */
    protected function buildValue() : string
    {
        $strHTML = '';
        $strValue = $this->oFG->oData->getBtnValue($this->strName);
        if (!empty($strValue)) {
            $strHTML = ' value="' . $strValue . '"';
        }
        return $strHTML;
    }
}
