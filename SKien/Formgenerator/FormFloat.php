<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Input field for float value.
 * By default the separators are tried to get from the local environment, but
 * they can be specified in the configuration.
 * The default align of the input field is right - this also can be overwritten
 * in the configuration.
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
*/
class FormFloat extends FormInput
{
    /** @var string decimal point     */
    protected string $strDP = '.';
    /** @var string thousands separator     */
    protected string $strTS = ',';
    /** @var int decimal digits     */
    protected int $iDec = 2;
    /** @var bool empty entries allowed. If false, empty input is set to '0.0' */
    protected bool $bEmptyAllowed = false;

    /**
     * Creates input field for float values.
     * @param string $strName   Name (if no ID specified, name is used also as ID)
     * @param int|string $size  number set the size-attribute, a string is used for the width attribute
     * @param int $wFlags       any combination of FormFlag constants
     */
    public function __construct(string $strName, $iSize, int $iDecimalPoints, int $wFlags = 0)
    {
        parent::__construct($strName, $iSize, $wFlags);
        $this->iDec = $iDecimalPoints;
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
        $iDecimalPoints = self::getAttribInt($oXMLElement, 'digits', 1);
        $wFlags = self::getAttribFlags($oXMLElement);
        $oFormElement = new self($strName, $strSize, $iDecimalPoints, $wFlags);
        $oFormParent->add($oFormElement);
        $oFormElement->readAdditionalXML($oXMLElement);
        return $oFormElement;
    }

    /**
     * get format from configuration: <ul>
     * <li> right alignment (default: true) </li>
     * <li> decimal point and thousands separator (default: from locale settings) </li></ul>
     */
    protected function onParentSet() : void
    {
        if ($this->oFG->getConfig()->getBool('Float.RightAlign', true)) {
            $this->addFlags(FormFlags::ALIGN_RIGHT);
        }

        $li = localeconv();

        $this->strDP = $this->oFG->getConfig()->getString('Float.DecimalPoint', ($li['mon_decimal_point'] ?: '.'));
        $this->strTS = $this->oFG->getConfig()->getString('Float.ThousandsSep', ($li['mon_thousands_sep'] ?: ','));

        $this->addAttribute('data-validation', 'float:' . ($this->bEmptyAllowed ? 'e' : 'x') . $this->iDec . $this->strDP . $this->strTS);
        $this->setPlaceholder($this->oFG->getConfig()->getString('Float.Placeholder'));
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::buildValue()
     */
    protected function buildValue() : string
    {
        $fltValue = floatval($this->oFG->getData()->getValue($this->strName));

        if ($this->oFlags->isSet(FormFlags::NO_ZERO) && $fltValue == 0) {
            return '';
        }

        $strValue = number_format($fltValue, $this->iDec, $this->strDP, $this->strTS);
        $strHTML = ' value="' . $strValue . '"';

        return $strHTML;
    }
}
