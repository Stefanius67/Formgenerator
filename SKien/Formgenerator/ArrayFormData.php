<?php
namespace SKien\Formgenerator;

/**
 * Data Class for the FormGenerator that can be initialized with arrays.
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class ArrayFormData extends AbstractFormData
{
    /**
     * @param array $aData
     * @param array $aSelectOptions
     */
    public function __construct(array $aData, array $aSelectOptions = [])
    {
        $this->aValues = $aData;
        $this->aSelectOptions = $aSelectOptions;
    }
}

