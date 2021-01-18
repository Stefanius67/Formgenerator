<?php
declare(strict_types=1);

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
        $this->oFlags = new FormFlags($wFlags);
        $this->strText = $strText;
    }
    
    /**
     * Build the HTML-notation for the static text
     * @return string
     */
    public function getHTML() : string 
    {
        // no container div!
        $this->addStyle('float', 'left');
        $strWidth = ($this->oParent ? $this->oParent->getColWidth() : '');
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
        // don't overwrite class if explicit set
        if (strlen($this->strClass)) {
            if ($this->oFlags->isSet(FormFlags::ERROR)) {
                $this->strClass = 'error';
            }
            if ($this->oFlags->isSet(FormFlags::HINT)) {
                $this->strClass = 'hint';
            }
            if ($this->oFlags->isSet(FormFlags::INFO)) {
                $this->strClass = 'forminfo';
            }
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
