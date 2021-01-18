<?php
namespace SKien\Formgenerator;

/**
 * Interface htat must be implementet by the object to pass form data to the
 * formgenerator
 *
 * #### History
 * - *2021-01-17*   initial version
 *
 * @package Formgenerator
 * @version 1.1.0
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
interface FormDataInterface
{
    /**
     * @param string $strName
     * @return mixed 
     */
    public function getValue(string $strName);
    /**
     * @param string $strName
     * @return array
     */
    public function getSelectOptions(string $strName) : array;
    /**
     * @param string $strName
     * @return string
     */
    public function getBtnValue(string $strName) : string;
}

