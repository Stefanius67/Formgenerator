<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Input field for currency value.
 * - always right aligned
 * - field will be added to JS form validation
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
class FormCur extends FormInput
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
     * Creates input field for currency values.
     * @param string $strName
     * @param int $iSize
     * @param int $wFlags    default value = 0
     */
    public function __construct(string $strName, int $iSize, int $wFlags = 0) 
    {
        parent::__construct($strName, $iSize, $wFlags);
    }
    
    /**
     * get format from configuration: <ul>
     * <li> right alignment (default: true) </li>
     * <li> currency symbol (default: from locale settings) </li>
     * <li> decimal point and thousands separator (default: from locale settings) </li></ul>
     */
    protected function onParentSet() : void
    {
        if ($this->oFG->getConfig()->getBool('Currency.RightAlign', true)) {
            $this->addFlags(FormFlags::ALIGN_RIGHT);
        }
        
        $li = localeconv();
        
        if ($this->oFlags->isSet(FormFlags::ADD_CUR)) {
            $this->strSuffix = $this->oFG->getConfig()->getString('Currency.Symbol', ($li['currency_symbol'] ?: 'USD'));
        }
        
        $this->strDP = $this->oFG->getConfig()->getString('Currency.DecimalPoint', ($li['mon_decimal_point'] ?: '.'));
        $this->strTS = $this->oFG->getConfig()->getString('Currency.ThousandsSep', ($li['mon_thousands_sep'] ?: ','));
        
        $this->addAttribute('data-validation', 'cur:' . ($this->bEmptyAllowed ? 'e' : 'x') . $this->iDec . $this->strDP . $this->strTS);
    }
    
    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::buildValue()
     */
    protected function buildValue() : string
    {
        $fltValue = floatval($this->oFG->getData()->getValue($this->strName));

        if ($this->oFlags->isSet(FormFlags::NO_ZERO) && $fltValue == 0.0) {
            return '';
        }
        
        $strValue = number_format($fltValue, 2, $this->strDP, $this->strTS);
        $strHTML = ' value="' . $strValue . '"';
        
        return $strHTML;
    }
}
