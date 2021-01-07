<?php
namespace SKien\Formgenerator;

/**
 * Element to display image inside of a form.
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
class FormImage extends FormElement
{
    /** @var string|int image to display     */
    protected $img;
    /** @var string CSS styles     */
    protected string $strStyle;
    /** @var string JS onclick() handler     */
    protected string $strOnClick;
    
    /**
     * Create image element. 
     * @param string $strName
     * @param string|int $img       image to display or index to a standard image
     * @param string $strOnClick    JS onclick() handler
     * @param string $strStyle      CSS style (default '')
     * @param string $strTitle      tooltip title (default '')
     */
    public function __construct(string $strName, $img, string $strOnClick, string $strStyle = '', string $strTitle = '') 
    {
        $this->strName = $strName;
        $this->img = $img;
        $this->strOnClick = $strOnClick;
        $this->setTitle($strTitle);

        if (strlen($strStyle) > 0) {
            $this->aStyle = self::parseStyle($strStyle);
        }
        if (!isset($this->aStyle['vertical-align'])) {
            $this->addStyle('vertical-align', 'text-bottom');
        }
    }

    /**
     * Build the HTML-notation for the image.
     * @return string
     */
    public function getHTML() : string
    {
        $strHTML = $this->buildContainerDiv();
        
        $strImg = is_numeric($this->img) ? $this->getStdImage(intval($this->img)) : $this->img;
        
        $strAlt = 'Grafik'; 
        $strHTML .= '<img src="' . $strImg . '" alt="' . $strAlt . '"';
        if (!empty($this->strName)) {
            $strHTML .= ' id="' . $this->strName . '"';
        }
        $strHTML .= $this->buildStyle();
        if (!empty($this->strOnClick)) {
            $strHTML .= ' onclick="' . $this->strOnClick . ';"';
        }
        $strHTML .= $this->buildClass();
        $strHTML .= $this->buildID();
        $strHTML .= $this->buildAttributes();
        $strHTML .= '></div>' . PHP_EOL;
        
        return $strHTML;
    }
}
