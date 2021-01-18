<?php
namespace SKien\Formgenerator;

/**
 *
 * @author sk
 *        
 */
abstract class AbstractFormData implements FormDataInterface
{
    protected array $aValues;
    protected array $aSelectOptions;
    protected array $aBtnValues;
    
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
     * (non-PHPdoc)
     * @see \SKien\Formgenerator\FormDataInterface::getSelectOptions()
     */
    public function getBtnValue(string $strName) : string
    {
        return $this->aBtnValues[$strName] ?? '';
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
     * Set selectiptions for specified elemnt
     * @param string $strName
     * @param array $aOptions
     */
    public function setSelectOptions(string $strName, array $aOptions) : void
    {
        $this->aSelectOptions[$strName] = $aOptions;
    }
    
    /**
     * Set value for specified element.
     * @param string $strName
     * @param mixed $value
     */
    public function setBtnValue(string $strName, $value) : void
    {
        $this->aBtnValues[$strName] = $value;
    }
}

