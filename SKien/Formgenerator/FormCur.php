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
    use GetFromSQL;
    
    /**
     * Creates input field for currency values.
     * @param string $strName
     * @param float $dblValue
     * @param int $iSize
     * @param int $wFlags    default value = 0
     */
    public function __construct(string $strName, float $dblValue, int $iSize, int $wFlags = 0) 
    {
        $strValue = $this->curFromSQL((string)$dblValue, false);
        $wFlags |= self::ALIGN_RIGHT;
        parent::__construct($strName, $strValue, $iSize, $wFlags);
        $this->strValidate = 'aCur';
    }
}
