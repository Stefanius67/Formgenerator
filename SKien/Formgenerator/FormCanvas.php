<?php
namespace SKien\Formgenerator;

/**
 * canvas element
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
class FormCanvas extends FormInput
{
    /** @var int internal width of the canvas     */
    protected int $iWidth;
    /** @var int internal height of the canvas     */
    protected int $iHeight;
    
    /**
     * Create o canvas element. 
     * @param string $strID
     * @param int $iWidth
     * @param int $iHeight
     * @param string $strStyle
     */
    public function __construct(string $strID, int $iWidth, int $iHeight, string $strStyle='') 
    {
        $this->strID = $strID;
        $this->iWidth = $iWidth;
        $this->iHeight = $iHeight;

        if (strlen($strStyle) > 0) {
            trigger_error('use AddStyle() instead of parameter strStyle!', E_USER_DEPRECATED);
            $this->aStyle = self::parseStyle($strStyle);
        }

        // Note: set attributes for width and height-styles will change internal behaviour of canvas
        $this->addAttribute('height', (string) $this->iHeight);
        $this->addAttribute('width', (string) $this->iWidth);
    }

    /**
     * build the HTML-notation for the cancas element
     *
     * @return string
     */
    public function getHTML() : string
    {
        $strHTML  =  $this->buildContainerDiv();

        $strHTML .= '   <canvas id="' . $this->strID . '"';
        $strHTML .= $this->buildStyle();
        $strHTML .= $this->buildAttributes();
        $strHTML .= '></canvas>' . PHP_EOL;
        $strHTML .= '</div>' . PHP_EOL;
        
        return $strHTML;
    }
}
