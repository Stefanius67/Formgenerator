<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Input field for currency value.
 * Special variant of FormFloat with
 * - fixed two decimalpoints.
 * - Separators can be specified alternating to the definition for the FormFloat
 * - Currency symbol can be appended
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
*/
class FormCur extends FormFloat
{
    /**
     * Creates input field for currency values.
     * @param string $strName   Name (if no ID specified, name is used also as ID)
     * @param int|string $size  number set the size-attribute, a string is used for the width attribute
     * @param int $wFlags       any combination of FormFlag constants
     */
    public function __construct(string $strName, $size, int $wFlags = 0)
    {
        parent::__construct($strName, $size, 2, $wFlags);
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     * @internal
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        $strName = self::getAttribString($oXMLElement, 'name', '');
        $strSize = self::getAttribString($oXMLElement, 'size', '');
        $wFlags = self::getAttribFlags($oXMLElement);
        $oFormElement = new self($strName, $strSize, $wFlags);
        $oFormParent->add($oFormElement);
        $oFormElement->readAdditionalXML($oXMLElement);
        return $oFormElement;
    }

    /**
     * get format from configuration: <ul>
     * <li> currency symbol (default: from locale settings) </li></ul>
     */
    protected function onParentSet() : void
    {
        parent::onParentSet();

        $li = localeconv();

        $this->strDP = $this->oFG->getConfig()->getString('Currency.DecimalPoint', $this->strDP);
        $this->strTS = $this->oFG->getConfig()->getString('Currency.ThousandsSep', $this->strTS);

        $this->addAttribute('data-validation', 'float:' . ($this->bEmptyAllowed ? 'e' : 'x') . $this->iDec . $this->strDP . $this->strTS);
        $this->setPlaceholder($this->oFG->getConfig()->getString('Currency.Placeholder'));

        if ($this->oFlags->isSet(FormFlags::ADD_CUR)) {
            $this->strSuffix = $this->oFG->getConfig()->getString('Currency.Symbol', ($li['currency_symbol'] ?: 'USD'));
        }
    }
}
