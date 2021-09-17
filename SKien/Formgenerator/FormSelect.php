<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * HTML select element.
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormSelect extends FormInput
{
    /** @var array available select option */
    protected ?array $aOptions = null;
    /** @var string text for selectbutton     */
    protected string $strSelectBtnText;

    /**
     * Create HTML select element.
     * If $wFlags contain SELECT_BTN property, the display of the selected
     * value is replaced by a button with text specified with setSelectBtnText.
     * (default text: 'Auswählen')
     * @see FormSelect::setSelectBtnText()
     *
     * @param string $strName    Name (if no ID specified, name is used also as ID)
     * @param int $iSize         size of list. 1 => dropdown list (default: 1)
     * @param int $wFlags        any combination of FormFlag constants
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
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     * @internal
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        $strName = self::getAttribString($oXMLElement, 'name');
        $iSize = self::getAttribInt($oXMLElement, 'size', 1);
        $wFlags = self::getAttribFlags($oXMLElement);
        $oFormElement = new self($strName, $iSize, $wFlags);
        $oFormParent->add($oFormElement);
        $oFormElement->readAdditionalXML($oXMLElement);
        return $oFormElement;
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::readAdditionalXML()
     * @internal
     */
    public function readAdditionalXML(\DOMElement $oXMLElement) : void
    {
        parent::readAdditionalXML($oXMLElement);
        if (self::hasAttrib($oXMLElement, 'selectbtntext')) {
            $this->setSelectBtnText(self::getAttribString($oXMLElement, 'selectbtntext'));
        }
        $oOptions = $oXMLElement->getElementsByTagName('option');
        if ($oOptions->length > 0) {
            $this->aOptions = [];
            foreach ($oOptions as $oOption) {
                $this->aOptions[$oOption->nodeValue] = self::getAttribString($oOption, 'value');
            }
        }
    }

    /**
     * Build the HTML code for the element.
     * @return string
     * @internal
     */
    public function getHTML() : string
    {
        $this->processFlags();

        $strSelect = $this->oFG->getData()->getValue($this->strName);
        $aOptions = $this->aOptions ?: $this->oFG->getData()->getSelectOptions($this->strName);

        $strWidth = ($this->oParent ? $this->oParent->getColWidth($this->iCol) : '');
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
        $strHTML .= $this->buildTabindex();
        $strHTML .= '>' . PHP_EOL;

        if (count($aOptions) > 0) {
            foreach ($aOptions as $strOption => $strValue) {
                $strHTML .= '           ';
                $strHTML .= '<option ';
                if ($strValue == $strSelect) {
                    $strHTML .= 'selected ';
                }
                if (strlen($strOption) == 0) {
                    // to prevent HTML5 validation error "Element option without attribute label must not be empty."
                    $strOption = '&nbsp;';
                }
                $strHTML .= 'value="' . $strValue . '">' . $strOption . '</option>' . PHP_EOL;
            }
        } else {
            // dropdown selectlist without options...
            trigger_error('empty select options set!', E_USER_NOTICE);
        }

        $strHTML .= '       </select></div>' . PHP_EOL;

        return $strHTML;
    }

    /**
     * Set text for selectbutton.
     * @param string $strSelectBtnText
     */
    public function setSelectBtnText(string $strSelectBtnText) : void
    {
        if (!$this->oFlags->isSet(FormFlags::SELECT_BTN)) {
            trigger_error('SELECT_BTN flag must be set!', E_USER_NOTICE);
        }
        $this->strSelectBtnText = $strSelectBtnText;
    }

    /**
     * Set the select options for the element.
     * If no selection options are passed to the element via this method, an
     * attempt is made in the getHTML () method to determine an assigned list
     * via the data provider.
     * @see AbstractFormData::getSelectOptions()
     * @param array $aOptions
     */
    public function setSelectOptions(array $aOptions) : void
    {
        $this->aOptions = $aOptions;
    }
}
