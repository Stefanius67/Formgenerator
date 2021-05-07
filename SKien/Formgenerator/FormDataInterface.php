<?php
namespace SKien\Formgenerator;

/**
 * Interface that must be implementet by the Data object that is passed 
 * as form data to the formgenerator
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
interface FormDataInterface
{
    /**
     * The FormGenerator tries to get the value for each input element through this
     * method by the name of the input element.
     * The return type depends on the type of input element, the data is fetched for.
     * @param string $strName
     * @return mixed 
     */
    public function getValue(string $strName);
    
    /**
     * The FormGenerator tries to get the select options for select fields through this
     * method by the name of the input element.
     * The FormGenerator expects an associative array, where the key must contain the available 
     * option names and the value the associated value. 
     * @param string $strName
     * @return array
     */
    public function getSelectOptions(string $strName) : array;
}

