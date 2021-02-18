<?php
namespace SKien\Formgenerator;

/**
 * Abstract class representing data for the FormGenerator.
 *
 * @package Formgenerator
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
abstract class AbstractFormData implements FormDataInterface
{
    /** @var array containing all data that can be edited by the form     */
    protected array $aValues;
    /** @var array containing select list to be displayed by the form     */
    protected array $aSelectOptions;
    
    /**
     * (non-PHPdoc)
     * @see \SKien\Formgenerator\FormDataInterface::getValue()
     */
    public function getValue(string $strName)
    {
        return $this->aValues[$strName] ?? '';
    }

    /**
     * (non-PHPdoc)
     * @see \SKien\Formgenerator\FormDataInterface::getSelectOptions()
     */
    public function getSelectOptions(string $strName) : array
    {
        return $this->aSelectOptions[$strName] ?? [];
    }
    
    /**
     * Set value for specified element.
     * @param string $strName
     * @param mixed $value
     */
    public function setValue(string $strName, $value) : void
    {
        $this->aValues[$strName] = $value;
    }
    
    /**
     * Set selections for specified elemnt
     * @param string $strName
     * @param array $aOptions
     */
    public function setSelectOptions(string $strName, array $aOptions) : void
    {
        $this->aSelectOptions[$strName] = $aOptions;
    }
}

