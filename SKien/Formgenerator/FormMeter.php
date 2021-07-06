<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Meter element.
 *
 * The meter element represents a scalar measurement within a known range
 * (defined by min/max), or a fractional value (also known as a gauge). <br/>
 * The element is particularly suitable when it is desired that the color
 * display should change dependent on certain boundaries.
 *
 * > The meter element should not be used to indicate progress (as in a progress bar). Use
 * > a progress element instead.
 * > The meter element also does not represent a scalar value of arbitrary range unless
 * > at least a maximum is known.
 * >
 * > Examples:
 * > - disk usage
 * > - temperature within defined boundaries
 * > - relevance of a query result
 * > - fraction of a voting population to have selected a particular candidate.
 *
 * In addition to the min / max range, a lower and upper bound (low/high) and an optimum
 * value for checking and color coding can optionally be specified.
 *
 * <b>Usage without optimum  value:  </b><br/>
 * If the value to be displayed is below the lower or above the upper bound, the output
 * is in a different color than within the bounds.
 *
 * <b>Usage including optimum  value:  </b><br/>
 * This means that the color display changes between three categories (bad / middle /
 * good), depending on the value.
 * 1. *bad-middle-good*, if the optimum value is between the min and low bound.
 * 2. *good-middle-bad*, if the optimum value is between the high and max bound.
 *
 * @SKienImage FormMeter.png
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormMeter extends FormInput
{
    /** default min value */
    protected const MIN_DEFAULT = 0;
    /** default max value */
    protected const MAX_DEFAULT = 1.0;

    /** @var float value */
    protected float $fltValue = 0;
    /** @var float min value */
    protected float $fltMin;
    /** @var float max value */
    protected float $fltMax;
    /** @var float low value */
    protected ?float $fltLow = null;
    /** @var float high value */
    protected ?float $fltHigh = null;
    /** @var float optimum value */
    protected ?float $fltOpt = null;

    /**
     * Create a range slider.
     * @param string $strName   name of the Element
     * @param string $strWidth  width
     * @param float $fltMin     min value
     * @param float $fltMax     max value
     * @param int $wFlags       any combination of FormFlag constants
     */
    public function __construct(string $strName, string $strWidth, float $fltMin = self::MIN_DEFAULT, float $fltMax = self::MAX_DEFAULT, int $wFlags = 0)
    {
        parent::__construct($strName, $strWidth, $wFlags);
        $this->addFlags(FormFlags::READ_ONLY);
        $this->fltMin = $fltMin;
        $this->fltMax = $fltMax;
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     * @internal l
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        $strName = self::getAttribString($oXMLElement, 'name', '');
        $strWidth = self::getAttribString($oXMLElement, 'width', '');
        $fltMin = self::getAttribFloat($oXMLElement, 'min') ?? self::MIN_DEFAULT;
        $fltMax = self::getAttribFloat($oXMLElement, 'max') ?? self::MAX_DEFAULT;
        $wFlags = self::getAttribFlags($oXMLElement);
        $oFormElement = new self($strName, $strWidth, $fltMin, $fltMax, $wFlags);
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
        $this->fltLow = self::getAttribFloat($oXMLElement, 'low');
        $this->fltHigh = self::getAttribFloat($oXMLElement, 'high');
        $this->fltOpt = self::getAttribFloat($oXMLElement, 'optimum');
    }

    /**
     * Set min/max value for the slider.
     * @param float $fltMin
     * @param float $fltMax
     */
    public function setMinMax(?float $fltMin, ?float $fltMax) : void
    {
        $this->fltMin = $fltMin ?? $this->fltMin;
        $this->fltMax = $fltMax ?? $this->fltMax;
    }

    /**
     * Set the upper/lower bound of the measured range and the optimum value.
     * @param float $fltLow
     * @param float $fltHigh
     * @param float $fltOpt
     */
    public function setMessureRange(?float $fltLow, ?float $fltHigh, ?float $fltOpt = null) : void
    {
        $this->fltLow = $fltLow;
        $this->fltHigh = $fltHigh;
        $this->fltOpt = $fltOpt;
    }

    /**
     * get value from dataprovider.
     * Value have to be retrieved earlier because it may be needed for the value-label
     * before the value of the range element is set.
     */
    protected function onParentSet() : void
    {
        $value = $this->oFG->getData()->getValue($this->strName);
        $this->fltValue = $value ? floatval($value) : $this->fltMin;
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::buildValue()
     */
    protected function buildValue() : string
    {
        $strHTML = ' value="' . $this->fltValue . '"';
        return $strHTML;
    }


    /**
     * Build the HTML-notation for the input element.
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::getHTML()
     * @internal
     */
    public function getHTML() : string
    {
        $this->addAttribute('min', (string)$this->fltMin);
        $this->addAttribute('max', (string)$this->fltMax);
        $this->addAttribute('low', (string)$this->fltLow);
        $this->addAttribute('high', (string)$this->fltHigh);
        $this->addAttribute('optimum', (string)$this->fltOpt);
        $this->addAttribute('autocomplete', 'off');

        if (!empty($this->size)) {
            $this->addStyle('width', $this->size);
        }
        $this->processFlags();

        $strInnerHTML = $this->getAttribute('title');
        if ($strInnerHTML === null) {
            $strInnerHTML = $this->buildTitle();
            $this->addAttribute('title', $strInnerHTML);
        }

        $this->strID = $this->strID ?: $this->strName;

        $strHTML = $this->buildContainerDiv();
        $strHTML .= '<meter ';
        $strHTML .= ' name="' . $this->strName . '"';
        $strHTML .= FormElement::buildClass(); // direct call of FormElement - we don't want 'Input_OK' ...
        $strHTML .= $this->buildID();
        $strHTML .= $this->buildStyle();
        $strHTML .= $this->buildAttributes();
        $strHTML .= $this->buildValue();
        $strHTML .= '>' . $strInnerHTML . '</meter>';
        $strHTML .= '</div>' . PHP_EOL;

        return $strHTML;
    }

    /**
     * Build a title from value and min/max settings.
     * @return string
     */
    protected function buildTitle() : string
    {
        return $this->fltValue . ' / ' . $this->fltMax;
    }
}
