<?php
namespace SKien\Formgenerator;

/**
 * Empty FormData used to initialize FormGenerator Data member.
 *
 * @package Formgenerator
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

