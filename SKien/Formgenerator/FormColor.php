<?php
namespace SKien\Formgenerator;

/**
 * Element to select Color
 * TODO:
 * How can the JS-script be referenced here!
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
class FormColor extends FormInput
{
    /**
     * creates input field for color values
     * @param string $strName
     * @param string $strValue
     * @param int $wFlags    default 0
     */
    public function __construct(string $strName, string $strValue, int $wFlags = 0) 
    {
        $iSize = ($wFlags & self::HIDDEN) == 0 ? 10 : -1;
        $wFlags |= self::ADD_COLOR_PICKER;
        parent::__construct($strName, $strValue, $iSize, $wFlags);
    }
}
