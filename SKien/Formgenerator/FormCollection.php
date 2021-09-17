<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Base-class for all elements that can have child elements.
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
abstract class FormCollection extends FormElement
{
    /** @var FormElementInterface[] all direct child elements     */
    protected array $aChild = [];
    /** @var array the width information for the cols inside this element     */
    protected ?array $aColWidth = null;
    /** @var string dimension of the width values ('%', 'px', 'em')     */
    protected string $strWidthDim = '%';

    /**
     * Create any kind of collection
     * @param int $wFlags
     */
    public function __construct(int $wFlags)
    {
        parent::__construct($wFlags);
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::readAdditionalXML()
     * @internal
     */
    public function readAdditionalXML(\DOMElement $oXMLElement) : void
    {
        parent::readAdditionalXML($oXMLElement);
        $strWidthDim = self::getAttribString($oXMLElement, 'widthdim', '%');
        if (self::hasAttrib($oXMLElement, 'colwidth')) {
            $aColWidth = self::getAttribIntArray($oXMLElement, 'colwidth');
            $this->setColWidth($aColWidth, $strWidthDim);
        }
    }

    /**
     * Add a child to this element.
     * Some properties of the element to add (parent, tabindex, ...) are changed/set
     * with the call of this method.
     * @param FormElementInterface $oElement element to add
     * @return FormElementInterface added element
     */
    public function add(FormElementInterface $oElement) : FormElementInterface
    {
        $oElement->setParent($this);
        $this->aChild[] = $oElement;
        $this->oFG->addElement($oElement);

        return $oElement;
    }

    /**
     * Set width for the cols included in this element.
     * > <b>Note:</b><br/>
     *   If the colwidth is not set for a collection, the element tries to get
     *   these settings from its parent element. If no colwidth is set there,
     *   the system continues with the next parent element until the property
     *   is set or the form generator element is reached.
     * @param array $aColWidth  numeric array of width for each col
     * @param string $strDim    dimension of the width values ('%', 'px', 'em')
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
     * @internal
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
     * @see FormDiv
     * @param int $iWidth   width of the div in percent
     * @param int $iAlign   align (`FormDiv::NONE`, `FormDiv::LEFT`, `FormDiv::RIGHT`, `FormDiv::CLEAR`)
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
     * @see FormFieldSet
     * @param string $strLegend text or image of the legend
     * @param string $strID  ID of the fieldset
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
     * Add new line to this fieldset
     * @see FormLine
     * @param string $strLabel (default: '&nbsp;')
     * @return \SKien\Formgenerator\FormLine
     */
    public function addLine(string $strLabel = '&nbsp;') : FormLine
    {
        $oFL = new FormLine($strLabel);
        $this->add($oFL);

        return $oFL;
    }

    /**
     * Build the HTML-notation for the element and/or all child elements.
     * @return string
     * @internal
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

    /**
     * Get styles from all child elements.
     * This method gives each element the chance to add special styles to the
     * current page.
     * @return string
     * @internal
     */
    public function getStyle() : string
    {
        $strStyle = '';
        $iCnt = count($this->aChild);
        for ($i = 0; $i < $iCnt; $i++) {
            $strStyle .= $this->aChild[$i]->getStyle();
        }
        return $strStyle;
    }
}

