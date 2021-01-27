<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Base-class for all elements that can have child elements.
 *
 * #### History
 * - *2021-01-18*   initial version
 *
 * @package Formgenerator
 * @version 1.1.0
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormContainer extends FormElement
{
    /** @var FormElement[] all direct child elements     */
    protected array $aChild = [];
    /** @var array the width information for the cols inside this element     */
    protected ?array $aColWidth = null;
    /** @var string dimension of the width values ('%', 'px', 'em')     */
    protected string $strWidthDim = '%';
    
    /**
     * @param int $wFlags
     */
    public function __construct(int $wFlags)
    {
        parent::__construct($wFlags);
    }
    
    /**
     * Add a child to this element.
     * @param FormElement $oElement
     * @return FormElement
     */
    public function add(FormElement $oElement) : FormElement
    {
        $oElement->setParent($this);
        $this->aChild[] = $oElement;
        $this->oFG->addElement($oElement);
        
        return $oElement;
    }
    
    /**
     * Set width for the cols included in this element.
     * @param array $aColWidth
     * @param string $strDim
     */
    public function setColWidth(array $aColWidth, string $strDim = '%') : void
    {
        $this->aColWidth = $aColWidth;
        $this->strWidthDim = $strDim;
    }
    
    /**
     * Get colwidth for requested element.
     * If no width set, we try to get the width throught the parent.
     * @param int $iCol  requested col, if -1 current col is used
     * @return string       colwidth including dimension
     */
    public function getColWidth(int $iCol = -1) : string
    {
        $strWidth = '';
        if ($iCol < 0) {
            $iCol = $this->iCol;
        }
        if ($this->aColWidth != null && $iCol < count($this->aColWidth) && $this->aColWidth[$iCol] >= 0) {
            $strWidth = $this->aColWidth[$iCol] . $this->strWidthDim;
        } else if ($this->oParent != null) {
            $strWidth = $this->oParent->getColWidth($iCol);
        }
        return $strWidth;
    }
    
    /**
     * Add a new div as child.
     * @param int $iWidth   width of the div in percent
     * @param int $iAlign   align (FormDiv::NONE, FormDiv::LEFT, FormDiv::RIGHT, FormDiv::CLEAR)
     * @param string $strID ID of the div
     * @return \SKien\Formgenerator\FormDiv created div element
     */
    public function addDiv(int $iWidth = 0, int $iAlign = FormDiv::CLEAR, string $strID = '') : FormDiv
    {
        $oDiv = new FormDiv($iWidth, $iAlign);
        $oDiv->SetID($strID);
        $this->add($oDiv);
        
        return $oDiv;
    }
    
    /**
     * Add a new fieldset to the element.
     * @param string $strLegend text or image of the legend
     * @param string $strID
     * @param int $iType type of the legend (FormFieldSet::TEXT or FormFieldSet::IMAGE)
     * @return \SKien\Formgenerator\FormFieldSet
     */
    public function addFieldSet(string $strLegend, string $strID = '', $iType = FormFieldSet::TEXT) : FormFieldSet
    {
        $oFS = new FormFieldSet($strLegend, $strID, $iType);
        $this->add($oFS);
        
        return $oFS;
    }
    
    /**
     * Build the HTML-notation for the element and/or all child elements.
     * @return string
     */
    public function getHTML() : string
    {
        $strHTML = '';
        $iCnt = count($this->aChild);
        for ($i = 0; $i < $iCnt; $i++) {
            $strHTML .= $this->aChild[$i]->GetHTML();
        }
        return $strHTML;
    }
}

