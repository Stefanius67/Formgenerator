<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Input field for currency value.
 * Special variant FormFloat with fixed two decimalpoints.
 * - Separators can be specified alternating to the definition for the FormFloat
 * - Currency symbol can be appended
 *
 * @package Formgenerator
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
*/
class FormCur extends FormFloat
{
    /**
     * Creates input field for currency values.
     * @param string $strName
     * @param int $iSize
     * @param int $wFlags    default value = 0
     */
    public function __construct(string $strName, int $iSize, int $wFlags = 0) 
    {
        parent::__construct($strName, $iSize, 2, $wFlags);
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
