<?php
namespace SKien\Formgenerator;

/**
 * Input element for integer value.
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
class FormInt extends FormInput
{
    /**
     * @param string $strName   name of input
     * @param int $lValue    initial value
     * @param int $iSize     size in digits
     * @param int $wFlags    flags (default = 0)
     */
    public function __construct(string $strName, int $lValue, int $iSize, int $wFlags = 0) 
    {
        if (($wFlags & self::READ_ONLY) == 0) {
            $this->strType = 'number';
        }
        $wFlags |= self::ALIGN_RIGHT;
        parent::__construct($strName, (string)$lValue, $iSize, $wFlags);
        $this->strValidate = 'aInt';
    }
    
    /**
     * set min/max value accepted by input field. 
     * @param int $iMin - set to null, if no limitation wanted
     * @param int $iMax - set to null, if no limitation wanted
     */
    public function setMinMax(?int $iMin, ?int $iMax) : void 
    {
        if ($iMin !== null) {
            $this->aAttrib['min'] = $iMin;
        }
        if ($iMax !== null) {
            $this->aAttrib['max'] = $iMax;
        }
    }
}
