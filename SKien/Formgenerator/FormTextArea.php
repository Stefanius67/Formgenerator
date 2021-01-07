<?php
namespace SKien\Formgenerator;

/**
 * Text Area element.
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
class FormTextArea extends FormInput
{
    /** @var int col count for the textarea     */
    protected int $iCols;
    /** @var int rowcount for the textarea     */
    protected int $iRows;
    
    /**
     * Create a textarea element.
     * @param string $strName
     * @param string $strValue
     * @param int $iCols
     * @param int $iRows
     * @param string $strWidth
     * @param int $wFlags
     */
    public function __construct(string $strName, string $strValue, int $iCols, int $iRows, string $strWidth = '95%', int $wFlags = 0) 
    {
        // CR only relevant for Textareas ... 
        if (($wFlags & self::REPLACE_BR_CR) != 0) {
            $strValue = str_replace('<br>', "\n", $strValue);
            $strValue = str_replace('<br/>', "\n", $strValue);
            $strValue = str_replace('<br />', "\n", $strValue);
        }
        parent::__construct($strName, $strValue, -1, $wFlags);
        $this->iCols = $iCols;
        $this->iRows = $iRows;
        $this->addStyle('width', $strWidth);
    }

    /**
     * Build the HTML-notation for the textarea
     * @return string
     */
    public function getHTML() : string
    {
        $strHTML  = $this->buildContainerDiv();
        
        $strHTML .= '<textarea';
        $strHTML .= ' class="' .    $this->strClass . '"';
        $strHTML .= ' name="' .     $this->strName . '"';
        $strHTML .= ' id="' .       $this->strName . '"';
        $strHTML .= ' cols="' .     $this->iCols . '"';
        $strHTML .= ' rows="' .     $this->iRows . '"';
        $strHTML .= $this->buildStyle();
        $strHTML .= $this->buildAttributes();
        $strHTML .= $this->buildTab($this->iTab);
        $strHTML .= '>' . $this->strValue . '</textarea>';
        $strHTML .= $this->buildSelectImage('picker_top');
        
        $strHTML .= '</div>' . PHP_EOL;
        
        return $strHTML;
    }
}
