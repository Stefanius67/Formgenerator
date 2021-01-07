<?php
namespace SKien\Formgenerator;

/**
 * Static Text element.
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
class FormStatic extends FormElement
{
    /** @var string the text to display     */
    protected string $strText;
    
    /**
     * Create a static text element.
     * @param string $strText
     * @param int $wFlags
     */
    public function __construct($strText, $wFlags = 0) 
    {
        $this->strText = $strText;
        $this->wFlags = $wFlags;
        $this->strID = '';
        $this->strClass = '';
    }
    
    /**
     * Build the HTML-notation for the static text
     * @return string
     */
    public function getHTML() : string 
    {
        // no container div!
        $this->addStyle('float', 'left');
        $strWidth = $this->getColWidth();
        if (!empty($strWidth)) {
            $this->addStyle('width', $strWidth);
        }
        if (($this->wFlags & self::HINT) != 0) {
            $this->strClass = 'hint';
        }
        if (($this->wFlags & self::ERROR) != 0) {
            // TODO: change from style='color: #FF0000' to class 'formerror'
            $this->addStyle('color', '#FF0000') ;
        }
        if (($this->wFlags & self::ALIGN_RIGHT) != 0) {
            $this->addStyle('text-align', 'right') ;
        } else if (($this->wFlags & self::ALIGN_CENTER) != 0) {
            $this->addStyle('text-align', 'center') ;
        }
        if (($this->wFlags & self::BOLD) != 0) {
            $this->addStyle('font-weight', 'bold') ;
        }
        if (($this->wFlags & self::INFO) != 0) {
            $this->strClass = 'forminfo';
        }
        
        $strHTML  = '';
        $strHTML .= '<div';
        $strHTML .= $this->buildID();
        $strHTML .= $this->buildClass();
        $strHTML .= $this->buildStyle();
        $strHTML .= $this->buildAttributes();
        $strHTML .= '>' . $this->strText . '</div>' . PHP_EOL;

        return $strHTML;
    }
}
