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
    /**
     * Creates input field for currency values.
     * @param string $strName
     * @param int $iSize
     * @param int $wFlags    default value = 0
     */
    public function __construct(string $strName, int $iSize, int $wFlags = 0) 
    {
        parent::__construct($strName, $iSize, $wFlags);
        $this->strValidate = 'aCur';
    }
    
    /**
     * We 'ask' the configuration for alignment.
     * $this->oFG is not available until the parent is set!
     */
    protected function onParentSet() : void
    {
        if ($this->oFG->getConfig()->getBool('Currency.RightAlign', true)) {
            $this->addFlags(FormFlags::ALIGN_RIGHT);
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::buildValue()
     */
    protected function buildValue() : string
    {
        $fltValue = floatval($this->oFG->getData()->getValue($this->strName));

        $li = localeconv();
        
        if ($this->oFlags->isSet(FormFlags::ADD_CUR)) {
            $this->strSuffix = $this->oFG->getConfig()->getString('Currency.Symbol', ($li['currency_symbol'] ?: 'USD'));
        }
        
        if ($this->oFlags->isSet(FormFlags::NO_ZERO) && $fltValue == 0.0) {
            return '';
        }
        
        $strDP = $this->oFG->getConfig()->getString('Currency.DecimalPoint', ($li['mon_decimal_point'] ?: '.'));
        $strTS = $this->oFG->getConfig()->getString('Currency.ThousandsSep', ($li['mon_thousands_sep'] ?: ','));
        
        $strValue = number_format($fltValue, 2, $strDP, $strTS);
        $strHTML = ' value="' . $strValue . '"';
        
        return $strHTML;
    }
}
