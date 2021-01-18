<?php
namespace SKien\Formgenerator;

/**
 * Empty FormData used to initialize FormGenerator Data member.
 *
 * #### History
 * - *2021-01-17*   initial version
 *
 * @package Formgenerator
 * @version 1.1.0
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class NullFormData extends AbstractFormData
{
    /**
     * create empty array for both iterators.
     */
    public function __construct()
    {
        $this->aValues = [];
        $this->aSelectOptions = [];
    }
}

