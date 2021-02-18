<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Static Text element.
 *
 * @package Formgenerator
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
        parent::__construct($wFlags);
    }
    
    /**
     * Build the HTML-notation for the static text
     * @return string
     */
    public function getHTML() : string 
    {
        // no container div!
        $this->addStyle('float', 'left');
        $strWidth = ($this->oParent ? $this->oParent->getColWidth($this->iCol) : '');
        if (!empty($strWidth)) {
            $this->addStyle('width', $strWidth);
        }
        if ($this->oFlags->isSet(FormFlags::ALIGN_RIGHT)) {
            $this->addStyle('text-align', 'right');
        } else if ($this->oFlags->isSet(FormFlags::ALIGN_CENTER)) {
            $this->addStyle('text-align', 'center');
        }
        if ($this->oFlags->isSet(FormFlags::BOLD)) {
            $this->addStyle('font-weight', 'bold');
        }
        $strSep = '';
        if (strlen($this->strClass) !== 0) {
            $strSep = ' ';
        }
        if ($this->oFlags->isSet(FormFlags::ERROR)) {
            $this->strClass .= $strSep . 'error';
        } else if ($this->oFlags->isSet(FormFlags::HINT)) {
            $this->strClass = $strSep . 'hint';
        } else if ($this->oFlags->isSet(FormFlags::INFO)) {
            $this->strClass = $strSep . 'forminfo';
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
