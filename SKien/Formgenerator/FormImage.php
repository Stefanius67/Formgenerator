<?php
declare(strict_types=1);

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
    /** standard delete image */
    const IMG_DELETE            = 1;
    /** standard search image */
    const IMG_SEARCH            = 2;
    /** standard search image */
    const IMG_BROWSE            = 3;
    /** standard image for date picker */
    const IMG_DATE_PICKER       = 4;
    /** standard image for time picker */
    const IMG_TIME_PICKER       = 5;
    /** standard image for dtu insert (DTU: Date-Time-User) */
    const IMG_DTU               = 6;
    
    /** @var string|int image to display     */
    protected $img;
    /** @var string CSS styles     */
    protected string $strStyle;
    /** @var string JS onclick() handler     */
    protected string $strOnClick;
    /** @var string image is bound to this input element    */
    protected string $strBoundTo = '';
    /** @var string image to use, if no image set    */
    protected string $strDefault = '';
    
    /**
     * Create image element. 
     * @param string $strName
     * @param string|int $img       image to display or index to a standard image
     * @param string $strOnClick    JS onclick() handler
     * @param int $wFlags       
     * @param string $strStyle      CSS style (default '')
     */
    public function __construct(string $strName, $img, string $strOnClick, int $wFlags = 0, string $strStyle = '')
    {
        parent::__construct($wFlags);
        $this->strName = $strName;
        $this->img = $img;
        $this->strOnClick = $strOnClick;

        if (strlen($strStyle) > 0) {
            $this->aStyle = self::parseStyle($strStyle);
        }
        if (!isset($this->aStyle['vertical-align'])) {
            $this->addStyle('vertical-align', 'text-bottom');
        }
    }
    
    /**
     * Bind the image to an input field that contains the imagepath.
     * @param string $strBoundTo
     */
    public function bindTo(string $strBoundTo) : void
    {
        $this->strBoundTo = $strBoundTo;
    }
    
    /**
     * Set a default image, if no image set or the bounded input contains no data.
     * @param string $strDefault
     */
    public function setDefault(string $strDefault) : void
    {
        $this->strDefault = $strDefault;
    }
    
    /**
     * Build the HTML-notation for the image.
     * @return string
     */
    public function getHTML() : string
    {
        if (strlen($this->strDefault) > 0) {
            $this->addAttribute('data-default', $this->strDefault);
        }
        
        $strStyle = '';
        if ($this->oFlags->isSet(FormFlags::ALIGN_CENTER)) {
            $strStyle = 'text-align: center;';
        } else if ($this->oFlags->isSet(FormFlags::ALIGN_RIGHT)) {
            $strStyle = 'text-align: right;';
        }
        $strHTML  = $this->buildContainerDiv($strStyle);
        
        if (strlen($this->strBoundTo) > 0) {
            $this->addAttribute('data-bound-to', $this->strBoundTo);
            $strImg = $this->oFG->getData()->getValue($this->strBoundTo);
        } else if (is_numeric($this->img)) {
            [$strImg, $strTitle] = $this->oFG->getStdImage(intval($this->img));
            if (strlen($strTitle) > 0) {
                $this->addAttribute('title', $strTitle);
            }
        } else {
            $strImg = $this->img;
        }
        
        if (strlen($strImg) == 0) {
            $strImg = $this->strDefault;
        }
        
        $strAlt = 'Image'; 
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
