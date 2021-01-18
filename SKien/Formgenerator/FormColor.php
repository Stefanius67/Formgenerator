<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Element to select Color
 * Uses the JSColor - picker from https://jscolor.com/
 * 
 * needs jscolor.js or jscolor.min.js
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
     * Creates input field for color values.
     * @param string $strName
     * @param int $wFlags    default 0
     */
    public function __construct(string $strName, int $wFlags = 0) 
    {
        $wFlags |= FormFlags::ADD_COLOR_PICKER;
        parent::__construct($strName, 10, $wFlags); // TODO: default size config
    }
}
