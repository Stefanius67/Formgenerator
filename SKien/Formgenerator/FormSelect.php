<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * HTML select element.
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
class FormSelect extends FormInput
{
    /** @var string text for selectbutton     */
    protected string $strSelectBtnText; 
    
    /**
     * Create HTML select element.
     * If $wFlags contain SELECT_BTN property, the display of the selected
     * value is replaced by a button with text specified with setSelectBtnText.
     * (default text: 'Auswählen')
     * @see FormSelect::setSelectBtnText()
     *  
     * @param string $strName    name and id
     * @param int $iSize         size of list. 1 => dropdown list (default: 1)
     * @param int $wFlags        flags (default: 0)
     */
    public function __construct(string $strName, int $iSize = 1, int $wFlags = 0)
    {
        parent::__construct($strName, $iSize, $wFlags);
        if ($this->oFlags->isSet(FormFlags::SELECT_BTN)) {
            $this->strClass .= ' sbSelect';
            if ($iSize != 1) {
                trigger_error('SELECT_BTN must have size of 1!', E_USER_WARNING);
            }
        }
        $this->strSelectBtnText = 'Auswählen';
    }
    
    /**
     * Build the HTML code for the element
     * @return string
     */
    public function getHTML() : string
    {
        $strSelect = $this->oFG->oData->getValue($this->strName);
        $aOptions = $this->oFG->oData->getSelectOptions($this->strName);
        
        $strWidth = ($this->oParent ? $this->oParent->getColWidth() : '');
        $strHTML  = '';
        $strHTML .= '       ';
        $strHTML .= '<div style="float: left; position: relative;';
        if (!empty($strWidth)) {
            $strHTML .= ' width: ' . $strWidth . ';';
        }
        $strHTML .= '">';
        if ($this->oFlags->isSet(FormFlags::SELECT_BTN)) {
            $strHTML .= '<button class="sbBtn">' . $this->strSelectBtnText . '</button>';
        }
        $strHTML .= '<select';
        $strHTML .= ' class="' . $this->strClass . '"';
        $strHTML .= ' name="' . $this->strName . '"';
        $strHTML .= ' id="' . $this->strName . '"';
        if ($this->size > 0) {
            $strHTML .= ' size="' . $this->size . '"';
        }
        $strHTML .= $this->buildStyle();
        $strHTML .= $this->buildAttributes();
        $strHTML .= $this->buildTab();
        $strHTML .= '>' . PHP_EOL;

        if (count($aOptions) > 0) {
            foreach ($aOptions as $strOption => $strValue) {
                $strHTML .= '           ';
                $strHTML .= '<option ';
                // TODO: define const for the separator value 
                if (is_numeric($strValue) && $strValue == -1000) {
                    $strHTML .= ' disabled class="separator" value=""></option>' . PHP_EOL;
                } else {
                    if ($strValue == $strSelect) {
                        $strHTML .= 'selected ';
                    }
                    $strHTML .= 'value="' . $strValue . '">' . $strOption . '</option>' . PHP_EOL;
                }
            }
        } else if ($this->size == 1) {
            // dropdown selectlist without options... 
            trigger_error('empty select options set!', E_USER_NOTICE);
        }
            
        $strHTML .= '       </select></div>' . PHP_EOL;
        
        return $strHTML;
    }
    
    /**
     * set text for selectbutton.
     * @param string $strSelectBtnText
     */
    public function setSelectBtnText(string $strSelectBtnText) : void 
    {
        if (!$this->oFlags->isSet(FormFlags::SELECT_BTN)) {
            trigger_error('SELECT_BTN flag must be set!', E_USER_NOTICE);
        }
        $this->strSelectBtnText = $strSelectBtnText;
    }
}
