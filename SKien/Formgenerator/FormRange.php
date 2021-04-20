<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Range slider element
 *
 * @package Formgenerator
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormRange extends FormInput
{
    /** @var int value */
    protected int $iValue = 0;
    /** @var int step */
    protected int $iStep = 1;
    /** @var int min value */
    protected int $iMin = 0;
    /** @var int max value */
    protected int $iMax = 100;
    
    /**
     * @param string $strName   name of input
     * @param string $strWidth  width
     * @param int $iMin
     * @param int $iMax
     * @param int $wFlags       flags (default = 0)
     */
    public function __construct(string $strName, string $strWidth, int $iMin=0, int $iMax=100, int $wFlags = 0) 
    {
        parent::__construct($strName, $strWidth, $wFlags);
        $this->strType = 'range';
        $this->iMin = $iMin;
        $this->iMax = $iMax;
    }
    
    /**
     * Set min/max value accepted by input field. 
     * @param int $iMin - set to null, if no limitation wanted
     * @param int $iMax - set to null, if no limitation wanted
     */
    public function setMinMax(?int $iMin, ?int $iMax) : void 
    {
        $this->iMin = $iMin ?? $this->iMin;
        $this->iMax = $iMax ?? $this->iMax;
    }
    
    /**
     * Set step width to be performed by slider.
     * @param int $iStep
     */
    public function setStep(int $iStep) : void
    {
        $this->iStep = $iStep;
    }
    
    /**
     * get value from dataprovider.
     * Value have to be retrieved earlier because it may be needed for the value-label
     * before the value of the range element is set. 
     */
    protected function onParentSet() : void
    {
        $this->iValue = intval($this->oFG->getData()->getValue($this->strName)) ?? $this->iMin;
    }
    
    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::buildValue()
     */
    protected function buildValue() : string
    {
        $strHTML = ' value="' . $this->iValue . '"';
        return $strHTML;
    }
    
    
    /**
     * Build the HTML-notation for the input element.
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::getHTML()
     */
    public function getHTML() : string
    {
        $this->addAttribute('step', (string)$this->iStep);
        $this->addAttribute('min', (string)$this->iMin);
        $this->addAttribute('max', (string)$this->iMax);
        $this->addAttribute('autocomplete', 'off');
        
        $this->processFlags();
        $strHTML = $this->buildContainerDiv('display: flex;');
        
        $this->strID = $this->strID ?: $this->strName;
        
        if ($this->oFlags->isSet(FormFlags::SHOW_VALUE) && !$this->oFlags->isSet(FormFlags::ALIGN_RIGHT)) {
            $strHTML .= $this->buildValueLabel();
        }
        $strHTML .= '<div class="slider" style="width: ' . $this->size . ';">';
        $strHTML .= '<input';
        $strHTML .= ' type="' . $this->strType . '"';
        $strHTML .= ' name="' . $this->strName . '"';
        $strHTML .= $this->buildClass();
        $strHTML .= $this->buildID();
        $strHTML .= $this->buildStyle();
        $strHTML .= $this->buildTabindex();
        $strHTML .= $this->buildAttributes();
        $strHTML .= $this->buildValue();
        $strHTML .= '>';
        $strHTML .= '</div>' . PHP_EOL;
        if ($this->oFlags->isSet(FormFlags::SHOW_VALUE) && $this->oFlags->isSet(FormFlags::ALIGN_RIGHT)) {
            $strHTML .= $this->buildValueLabel();
        }
        $strHTML .= '</div>' . PHP_EOL;
        
        return $strHTML;
    }
    
    /**
     * Build the label to display the actual value selected by the range element.
     * @return string
     */
    protected function buildValueLabel() : string
    {
        // if min is negative it can be longer than the max value...
        $iLen = (strlen((string)$this->iMin) > strlen((string)$this->iMax)) ? strlen((string)$this->iMin) : strlen((string)$this->iMax);
        
        $strHTML = '<label class="slider_label"';
        $strHTML .= ' for="' . $this->strID . '"';
        $strHTML .= ' style="width: ' . $iLen . 'em;';
        if ($this->oFlags->isSet(FormFlags::ALIGN_RIGHT)) {
            $strHTML .= ' text-align: right;';
        }
        $strHTML .= '">';
        $strHTML .= $this->iValue;
        $strHTML .= '</label>' . PHP_EOL;
        
        return $strHTML;
    }
}
