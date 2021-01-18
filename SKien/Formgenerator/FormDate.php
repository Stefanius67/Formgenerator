<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Input field for date value.
 * - size always 10
 * - field will be added to JS form validation
 * 
 * The use of HTML5 type="date" causes following problems:
 * - width is dependent on browser, ignores requested size and changes (increase) 
 *   sometimes when datepicker was used !!
 * - not supported by every browser so far
 * - have to be set in YYYY-MM-DD and item.value contains date in YYYY-MM-DD
 *    -> internal logic must be changed (set no problem - DB saving is more tricky...)
 *    -> own JS validation have to be disabled...
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
class FormDate extends FormInput
{
    /**
     * Creates input field for date values.
     * @param string $strName
     * @param int $wFlags
     */
    public function __construct(string $strName, int $wFlags = 0) 
    {
        parent::__construct($strName, 10, $wFlags);
        $this->strValidate = 'aDate';
    }
}
