<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Class to create a line starting with label.
 * - create as child of a FormField using FormFieldSet::addLine().
 * - create standalone and add directly to form  
 *
 * #### History
 * - *2020-05-12*   initial version
 * - *2021-01-07*   PHP 7.4
 *
 * @package Formgenerator
 * @version 1.1.0
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormLine extends FormElement
{
    /** @var string text for the line label     */
    protected string $strLabel;
    /** @var int col count     */
    protected int $iCols = 0;
    
    public function __construct(string $strLabel)
    {
        $this->strLabel = $strLabel;
        $this->iCol = 0;
        $this->strID = '';
    }

    /**
     * Add a child to the line.
     * next col index is passed to the element and the col count is inkremented with 
     * each element added to this line.
     * @param FormElement $oElement
     * @return FormElement
     */
    public function add(FormElement $oElement) : FormElement
    {
        parent::add($oElement);
        $oElement->setCol(++$this->iCols);
        
        return $oElement;
    }

    /**
     * Build the HTML code for the element.
     * The line is 'abstract' and representet by a div. <br/>
     * All direct child elements are generated inside this div.
     * @return string
     */
    public function getHTML() : string
    {
        $strWidth = $this->getColWidth();
        if (!empty($strWidth)) {
            $this->addStyle('width', $strWidth);
        }
        
        $strHTML  = '';
        $strHTML .= '   <div';
        $strHTML .= $this->buildID();
        $strHTML .= ">" . PHP_EOL;
        $strHTML .= '       <label';
        $strHTML .= $this->buildStyle();
        $strHTML .= '>' . $this->strLabel . '</label>' . PHP_EOL;
        $iCnt = count($this->aChild);
        for ($i = 0; $i < $iCnt; $i++) {
            $strHTML .= $this->aChild[$i]->getHTML();
        }
        $strHTML .= '       <br style="clear:both;" />' . PHP_EOL;
        $strHTML .= '   </div>' . PHP_EOL;
        return $strHTML;
    }
}
