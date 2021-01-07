<?php
namespace SKien\Formgenerator;

/**
 * HTML form button element (inpt type="button")
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
class FormButton extends FormInput
{
    /** @var string onClick() JS handler     */
    protected string $strOnClick;

    /**
     * create button element
     * TODO: option to center button inside of containerdiv! 
     * @param string $strId        button id
     * @param string $strBtnText   button text (value)
     * @param string $strOnClick   onClick() handler
     * @param string $strStyle      CSS style(s) (default: '')
     */
    public function __construct(string $strId, string $strBtnText, string $strOnClick, string $strStyle = '') 
    {
        $this->strValue = $strBtnText;
        $this->strName = $strId;
        $this->strOnClick = $strOnClick;
        
        if (strlen($strStyle) > 0) {
            $this->aStyle = self::parseStyle($strStyle);
        }
    }

    /**
     * build the HTML-notation for the button
     * @return string
     */
    public function getHTML() : string
    {
        $strHTML  = $this->buildContainerDiv();

        $strHTML .= '<input type=button ';
        if (!empty($this->strName)) {
            $strHTML .= ' id="' . $this->strName . '"';
        }
        $strHTML .= $this->buildStyle();
        $strHTML .= $this->buildAttributes();
        if (!empty($this->strOnClick)) {
            $strHTML .= ' onclick="' . $this->strOnClick . ';" ';
        }
        $strHTML .= 'value="' . $this->strValue . '"></div>' . PHP_EOL;

        return $strHTML;
    }
}
