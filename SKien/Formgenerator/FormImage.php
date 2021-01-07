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
    // TODO: Find a more general way of defining standard images. (possibly via a config)
    /** standard delete image */
    const IMG_DELETE            =  1;
    /** standard image for date picker */
    const IMG_DATE_PICKER       =  2;
    
    /** @var string image to display     */
    protected string $strImg;
    /** @var string CSS styles     */
    protected string $strStyle;
    /** @var string JS onclick() handler     */
    protected string $strOnClick;
    
    /**
     * Create image element. 
     * @param string $strName
     * @param string|int $img   image to display or index to a standard image
     * @param string $strOnClick    JS onclick() handler
     * @param string $strStyle  default ''
     * @param string $strTitle  default ''
     */
    public function __construct(string $strName, $img, string $strOnClick, string $strStyle = '', string $strTitle = '') 
    {
        $this->strName = $strName;
        $this->strImg = is_numeric($img) ? $this->getStdImage(intval($img)) : $img;
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
        
        $strAlt = 'Grafik'; 
        $strHTML .= '<img src="' . $this->strImg . '" alt="' . $strAlt . '"';
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
    
    /**
     * Get filename for predifined standard images
     * @param int $iImage
     * @return string
     */
    protected function getStdImage(int $iImage) : string
    {
        // TODO: Find a more general way of defining standard images. (possibly via a config)
        $aImage = array( 
            self::IMG_DELETE          => '../images/16x16/admin_delete.png', 
            self::IMG_DATE_PICKER     => '../images/16x16/datepicker.png',
        );
        
        $strImg = '';
        if (isset($aImage[$iImage])) {
            $strImg = $aImage[$iImage];
        }
        return $strImg;
    }
}
