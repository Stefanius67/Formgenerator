<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Input field for time value.
 * - size always 10
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
class FormTime extends FormInput
{
    /**
     * Creates input field for time values.
     * @param string $strName
     * @param int $wFlags
     */
    public function __construct(string $strName, int $wFlags = 0) 
    {
        parent::__construct($strName, 10, $wFlags);
        $this->strValidate = 'aTime';
    }
}
