<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Group of radio buttons.
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormRadioGroup extends FormInput
{
    /** @var array<string,string> available select option */
    protected ?array $aOptions = null;

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
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::readAdditionalXML()
     * @internal
     */
    public function readAdditionalXML(\DOMElement $oXMLElement) : void
    {
        parent::readAdditionalXML($oXMLElement);
        $oOptions = $oXMLElement->getElementsByTagName('option');
        if ($oOptions->length > 0) {
            $this->aOptions = [];
            foreach ($oOptions as $oOption) {
                $this->aOptions[$oOption->nodeValue] = self::getAttribString($oOption, 'value');
            }
        }
    }

    /**
     * Build the HTML-markup for the radio group.
     * @return string
     * @internal l
     */
    public function getHTML() : string
    {
        $this->processFlags();

        $strSelect = $this->oFG->getData()->getValue($this->strName);
        $aOptions = $this->aOptions ?: $this->oFG->getData()->getSelectOptions($this->strName);

        $strHTML = $this->buildContainerDiv();

        $iBtn = 0;
        $this->addStyle('float', 'left');
        if (count($aOptions) > 0) {
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
        } else {
            // radiogroup without options...
            trigger_error('empty radiogroup defined!', E_USER_NOTICE);
        }

        $strHTML .= '</div>' . PHP_EOL;

        return $strHTML;
    }

    /**
     * Set the select options for the element.
     * If no selection options are passed to the element via this method, an
     * attempt is made to determine an assigned list via the data provider.
     * @param array<string,string> $aOptions
     */
    public function setSelectOptions(array $aOptions) : void
    {
        $this->aOptions = $aOptions;
    }
}
