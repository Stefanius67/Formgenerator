<?php
namespace SKien\Formgenerator;

/**
 *  Represents a <div> inside of the form.
 *  It can be used to arrange some fieldset horizontally or to group some 
 *  elements for dynamic hide/show operations
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
class FormDiv extends FormElement
{
    const   NONE    = -1;
    const   CLEAR   = 0;
    const   LEFT    = 1;
    const   RIGHT   = 2;
    
    /** @var int width of the div in percent     */
    protected int  $iWidth;
    /** @var int align of the div (FormDiv::NONE, FormDiv::LEFT, FormDiv::RIGHT, FormDiv::CLEAR)     */
    protected int  $iAlign;

    /**
     * Create a Div element
     * @param int $iWidth
     * @param int $iAlign
     */
    public function __construct(int $iWidth = 0, int $iAlign = self::CLEAR) : void
    {
        $this->iAlign = $iAlign;
        $this->iWidth = $iWidth;
        if ($this->iWidth > 0) {
            $this->addStyle('width', $this->iWidth . '%');
        }
        switch ($this->iAlign) {
        case self::CLEAR:
            $this->addStyle('clear', 'both');
            break;
        case self::LEFT:
            $this->addStyle('float', 'left');
            break;
        case self::RIGHT:
            $this->addStyle('float', 'right');
            break;
        case self::NONE:
        default:
            break;
        }
    }

    /**
     * Build the HTML-notation for the div element.
     */
    public function getHTML() : string
    {
        $strHTML  = '';
        $strHTML .= '<div';
        $strHTML .= $this->buildID();
        $strHTML .= $this->buildStyle();
        $strHTML .= $this->buildAttributes();
        $strHTML .= ">" . PHP_EOL;
        for ($i = 0; $i < count($this->aChild); $i++) {
            $strHTML .= $this->aChild[$i]->GetHTML();
        }
        $strHTML .= '</div>' . PHP_EOL;
        return $strHTML;
    }
}

