<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Radiogroup input.
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormRadioGroup extends FormInput
{
    /**
     * Create a radio group.
     * @param string $strName   name AND id of the element
     * @param int $wFlags    (default: 0)
     */
    public function __construct(string $strName, int $wFlags = 0) 
    {
        $this->oFlags = new FormFlags($wFlags);
        $this->strName = $strName;
        if ($this->oFlags->isSet(FormFlags::READ_ONLY | FormFlags::DISABLED)) {
            $this->addAttribute('disabled');
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        $strName = self::getAttribString($oXMLElement, 'name', '');
        $wFlags = self::getAttribFlags($oXMLElement);
        $oFormElement = new self($strName, $wFlags);
        $oFormParent->add($oFormElement);
        $oFormElement->readAdditionalXML($oXMLElement);
        return $oFormElement;
    }
    
    /**
     * Build the HTML-markup for the checkbox.
     * Checkbox is a special case, as it behaves particularly with regard to the transferred 
     * value and does not support read-only mode.
     * There is special treatment for both cases, which is explained in more detail in the 
     * respective code areas bolow.  
     * @return string
     */
    public function getHTML() : string 
    {
        $this->processFlags();
        
        $strSelect = $this->oFG->getData()->getValue($this->strName);
        $aOptions = $this->oFG->getData()->getSelectOptions($this->strName);
        
        $strHTML = $this->buildContainerDiv();
        
        // TODO: create surrogates for unchecked/readonly (see FormCheck)
        
        $iBtn = 0;
        $this->addStyle('float', 'left');
        foreach ($aOptions as $strName => $strValue) {
            if ($strName !== '') {
                $strHTML .= '<input type="radio"';
                $strHTML .= $this->buildStyle();
                $strHTML .= $this->buildAttributes();
                $strHTML .= $this->buildTabindex();
                $strHTML .= ' id="' . $this->strName . ++$iBtn . '"';
                $strHTML .= ' name="' . $this->strName . '"';
                if ($strSelect === $strValue) {
                    $strHTML .= ' checked';
                }
                $strHTML .= ' value="' . $strValue . '">';
                $strHTML .= ' <label for="' . $this->strName . $iBtn . '"';
                if ($this->oFlags->isSet(FormFlags::HORZ_ARRANGE)) {
                    $strHTML .= ' style="float: left;"';
                }
                $strHTML .= ' class="radio">' . $strName . '</label>' . PHP_EOL;
            }
        }
        
        $strHTML .= '</div>' . PHP_EOL;

        return $strHTML;
    }
}
