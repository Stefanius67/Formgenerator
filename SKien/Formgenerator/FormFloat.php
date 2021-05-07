<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Input field for float value.
 * Default the separators tried to get from the local environment, but can be
 * specified in the configuration.
 * Default align is right - this can be overwritten ion the configuration.
 * 
 * #### History
 * - *2021-01-30*   initial version
 *
 * @package Formgenerator
 * @version 1.1.0
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
     * @param string $strName
     * @param int $iSize
     * @param int $wFlags    default value = 0
     */
    public function __construct(string $strName, int $iSize, int $iDecimalPoints, int $wFlags = 0) 
    {
        parent::__construct($strName, $iSize, $wFlags);
        $this->iDec = $iDecimalPoints;
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
