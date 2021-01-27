<?php
declare(strict_types=1);

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
    /**
     * Create o canvas element. 
     * @param string $strID
     * @param int $iWidth
     * @param int $iHeight
     */
    public function __construct(string $strID, int $iWidth, int $iHeight) 
    {
        $this->oFlags = new FormFlags();
        $this->strID = $strID;

        // Note: set attributes for width and height-styles will change internal behaviour of canvas
        $this->addAttribute('height', (string)$iHeight);
        $this->addAttribute('width', (string)$iWidth);
    }

    /**
     * build the HTML-notation for the cancas element
     *
     * @return string
     */
    public function getHTML() : string
    {
        $strHTML = $this->buildContainerDiv();

        $strHTML .= '   <canvas id="' . $this->strID . '"';
        $strHTML .= $this->buildStyle();
        $strHTML .= $this->buildAttributes();
        $strHTML .= '></canvas>' . PHP_EOL;
        $strHTML .= '</div>' . PHP_EOL;
        
        return $strHTML;
    }
}
