<?php
namespace SKien\Formgenerator;

/**
 *
 * @author sk
 *        
 */
class ArrayFormData extends AbstractFormData
{

    /**
     * 
     * @param array $aData
     * @param array $aSelectOptions
     */
    public function __construct(array $aData, array $aSelectOptions = [])
    {
        $this->aValues = $aData;
        $this->aSelectOptions = $aSelectOptions;
    }
}

