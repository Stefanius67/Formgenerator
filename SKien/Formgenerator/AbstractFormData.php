<?php
namespace SKien\Formgenerator;

/**
 * Abstract class representing data for the FormGenerator.
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
abstract class AbstractFormData implements FormDataInterface
{
    /** @var array containing all data that can be edited by the form     */
    protected array $aValues;
    /** @var array containing select list to be displayed by the form     */
    protected array $aSelectOptions;

    /**
     * Get the value for the specified element.
     * @param string $strName   name of the element
     * @return mixed empty string, if no value for requested element available
     */
    public function getValue(string $strName)
    {
        return $this->aValues[$strName] ?? '';
    }

    /**
     * Get the select options for specified element.
     * @param string $strName   name of the element
     * @return array empty array, if no options for requested element available
     */
    public function getSelectOptions(string $strName) : array
    {
        return $this->aSelectOptions[$strName] ?? [];
    }

    /**
     * Set value for specified element.
     * @param string $strName   name of the element
     * @param mixed $value      value to set
     */
    public function setValue(string $strName, $value) : void
    {
        $this->aValues[$strName] = $value;
    }

    /**
     * Set select options for specified element.
     * @param string $strName  name of the element
     * @param array $aOptions  associative array with 'option' => value elements
     */
    public function setSelectOptions(string $strName, array $aOptions) : void
    {
        $this->aSelectOptions[$strName] = $aOptions;
    }
}

