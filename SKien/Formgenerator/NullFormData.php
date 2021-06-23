<?php
namespace SKien\Formgenerator;

/**
 * Empty FormData class.
 *
 * If no formdata is passed to the formgenerator, instance of this class is
 * created internaly. This ensures that a valid instance is always set
 * internally and this not have to be checked at every point in the code
 * where the data provider is accessed.
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
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
