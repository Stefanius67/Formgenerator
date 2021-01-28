<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Base-class for all elements of a form.
 * Starting with a FormGenerator-element, the form can contain any count 
 * of elements.
 * The elements usuallay be arranged within FormFieldSet- and FormLine-elements
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
abstract class FormElement
{
    // TODO: Find a more general way of defining standard images. (possibly via a config)
    /** standard delete image */
    const IMG_DELETE            = 1;
    /** standard delete image */
    const IMG_SEARCH            = 2;
    /** standard image for date picker */
    const IMG_DATE_PICKER       = 3;
    /** standard image for time picker */
    const IMG_TIME_PICKER       = 4;
    /** standard image for dtu insert (DTU: Date,Time,User) */
    const IMG_DTU               = 5;
    
    /** @var FormGenerator the FormGenerator this element belongs to     */
    protected FormGenerator $oFG;
    /** @var FormContainer the parent element - only FormGenerator must has no parent     */
    protected ?FormContainer $oParent = null;
    /** @var int tab index of the element if it can get focus     */
    protected int $iTabindex = -1;
    /** @var int col inside current line     */
    protected int $iCol = 0;
    /** @var string element name     */
    protected string $strName = '';
    /** @var string element id     */
    protected string $strID = '';
    /** @var string CSS class of the element     */
    protected string $strClass = '';
    /** @var FormFlags flags that specify the appearance and behaviour     */
    protected FormFlags $oFlags;
    /** @var array attributes of the element     */
    protected ?array $aAttrib = null;
    /** @var array (CSS) styles of the element     */
    protected ?array $aStyle = null;
    /** @var bool set to true, if element creates some JS     */
    protected bool $bCreateScript = false;
    /** @var bool set to true, if element creates some CSS style     */
    protected bool $bCreateStyle = false;
    
    /**
     * @param int $wFlags
     */
    public function __construct(int $wFlags)
    {
        $this->oFlags = new FormFlags($wFlags);
    }

    /**
     * Return the FormGenerator this element belongs to.
     * @return FormGenerator
     */
    public function getFG() : ?FormGenerator
    {
        return $this->oFG;
    }

    /**
     * Set the parent of this element.
     * The Formgenerator of the parent is adopted for this element. 
     * If there are global flags set for the FormGenerator, this flags are added.
     * @param FormContainer $oParent
     */
    public function setParent(FormContainer $oParent) : void 
    {
        $this->oParent = $oParent;
        $this->oFG = $oParent->oFG;
        $this->addFlags($this->oFG->getGlobalFlags());
        $this->onParentSet();
    }
    
    /**
     * Set the current col.
     * If this element is added as a child of a FormLine, the column is set in 
     * order to be able to calculate the correct width of the element.
     * @param int $iCol
     */
    public function setCol(int $iCol) : void
    {
        $this->iCol = $iCol;
    }
    
    /**
     * Set ID for the element.
     * @param string $strID
     */
    public function setID(string $strID) : void 
    {
        $this->strID = $strID;
    }
    
    /**
     * Set text for the elements title attribute.
     * @param string $strTitle
     */
    public function setTitle(string $strTitle) : void 
    {
        if (strlen($strTitle) > 0) {
            $this->addAttribute('title', $strTitle);
        }
    }
    
    /**
     * Set the tab index of the element.
     * Method is called from the PageGenerator after an element is added to the form.
     * @param int $iTabindex
     * @return int the number of indexes, the element needs
     */
    public function setTabindex(/** @scrutinizer ignore-unused */ int $iTabindex) : int
    {
        return 0;
    }
    
    /**
     * Add flags to element. 
     * @param int $wFlags
     */
    public function addFlags(int $wFlags) : void 
    {
        $this->oFlags->add($wFlags);
    }
    
    /**
     * Add any attribute.
     * If the attribute allready exist, the value will be overwritten.
     * @param string $strName
     * @param string $strValue
     */
    public function addAttribute(string $strName, string $strValue = '') : void 
    {
        $strName = strtolower($strName);
        if ($this->aAttrib == null) {
            $this->aAttrib = array();
        }
        if ($strName == 'style') {
            // style should no longer be set through AddAttribute()
            trigger_error('use AddStyle() to define additional styles for element!', E_USER_ERROR);
        }
        $this->aAttrib[$strName] = $strValue;
    }

    /**
     * Add any style.
     * If the attribute allready exist, the value will be overwritten.
     * @param string $strName
     * @param string $strValue
     */
    public function addStyle(string $strName, string $strValue) : void 
    {
        $strName = strtolower($strName);
        if ($this->aStyle == null) {
            $this->aStyle = array();
        }
        $this->aStyle[$strName] = $strValue;
    }

    /**
     * Set the CSS class of the element.
     * Any previously setting will be overwritten.
     * @param string $strClass
     */
    public function setClass(string $strClass) : void 
    {
        $this->strClass = $strClass;
    }
    
    /**
     * Add additional class to element.
     * Class is added to the existing classname separated with a blank <br/>
     * Generates a notice, if no class set so far!
     * @param string $strClass
     */
    public function addClass(string $strClass) : void 
    {
        if (strlen($this->strClass) == 0) {
            trigger_error('no class set so far!', E_USER_NOTICE);
        }
        $this->strClass .= ' ' . $strClass;
    }
    
    /**
     * Get JS script related to this element.
     * This method gives each element the chance to add special JS script to the 
     * current page. <br/>
     * <b>This method is only called for elements having member bCreateScript set to true!</b>
     * @return string
     */
    public function getScript() : string
    {
        return '';
    }
    
    /**
     * Get styles related to this element.
     * This method gives each element the chance to add special styles to the 
     * current page. <br/>
     * <b>This method is only called for elements having member bCreateStyle set to true!</b>
     * @return string
     */
    public function getStyle() : string
    {
        return '';
    }

    /**
     * @return string
     */
    abstract public function getHTML() : string;

    /**
     * Method called, after parent amd formgenerator is set properly.
     * Enhancing classes can use this method, to initialize properties that
     * nneds the parent or formgenerator (... configuration, global settings) 
     */
    protected function onParentSet() : void
    {
    }
    
    /**
     * Build the 'container' div arround the current element.
     * Additional styles (alignment, ...) can be passed.
     * @param string $strStyle
     * @return string
     */
    protected function buildContainerDiv(string $strStyle = '') : string
    {
        if (strpos($strStyle, 'float') === false) {
            $strStyle = 'float: left; ' . $strStyle;
        }
        $strWidth = ($this->oParent ? $this->oParent->getColWidth($this->iCol) : '');
        if (!empty($strWidth)) {
            $strStyle = rtrim($strStyle, ';');
            $strStyle .= '; width: ' . $strWidth . ';';
        }
        $strHTML = '<div style="' . $strStyle . '">';

        return $strHTML;
    }
    
    /**
     * Build the style attribute for the element.
     * @return string
     */
    protected function buildStyle() : string
    {
        $strStyle = '';
        if ($this->aStyle != null) {
            $strStyle = ' style="';
            foreach ($this->aStyle as $strName => $strValue) {
                $strStyle .= ' ' . $strName . ': ' . $strValue . ';';
            }
            $strStyle .= '"';
        }
        return $strStyle;
    }

    /**
     * Build all defined attributes for the element.
     * @return string
     */
    protected function buildAttributes() : string
    {
        $strAttrib = '';
        if ($this->aAttrib != null) {
            foreach ($this->aAttrib as $strName => $strValue) {
                $strAttrib .= ' ' . $strName;
                if (strlen($strValue) > 0) {
                    $strAttrib .= '="' . $strValue . '"';
                }
            }
        }
        return $strAttrib;
    }
    
    /**
     * Build the markup for the value attribute.
     * Retrieve value for the element from the FormData. 
     * @return string Empty string if no value set, complete attribute if set
     */
    protected function buildValue() : string
    {
        $strHTML = '';
        $strValue = $this->oFG->getData()->getValue($this->strName);
        
        if ($this->oFlags->isSet(FormFlags::TRIM)) {
            $strValue = trim($strValue);
        }
        if (!$this->oFlags->isSet(FormFlags::NO_ZERO) || ($strValue != 0 && $strValue != '0')) {
            $strHTML = ' value="' . str_replace('"', '&quot;', $strValue) . '"';
        }
        return $strHTML;
    }

    /**
     * Build the markup for the class attribute.
     * @return string Empty string if no class set, complete attribute if set
     */
    protected function buildClass() : string
    {
        $strClass = '';
        if (!empty($this->strClass)) {
            $strClass .= ' class="' . $this->strClass . '"';
        }
        return $strClass;
    }

    /**
     * Build the markup for the ID attribute.
     * @return string Empty string if no id set, complete attribute if set
     */
    protected function buildID() : string
    {
        $strID = '';
        if (!empty($this->strID)) {
            $strID .= ' id=' . $this->strID;
        }
        return $strID;
    }
    
    /**
     * Build the markup for the tabindex attribute.
     * @return string Empty string if $iTabindex = 0, complete attribute if set
     */
    protected function buildTabindex() : string
    {
        $strTabindex = '';
        if ($this->iTabindex > 0) {
            $strTabindex = ' tabindex="' . $this->iTabindex . '"';
        }
        return $strTabindex;
    }
    
    /**
     * Get filename for predifined standard images
     * @param int $iImage
     * @return string
     */
    protected function getStdImage(int $iImage) : string
    {
        // TODO: Find a more general way of defining standard images. (possibly via a config)
        $strPath = '../images/';
        if ($this->oFG !== null) {
            $strPath = $this->oFG->getImagePath();
        }
        $aImage = array(
            self::IMG_DELETE                => '16x16/admin_delete.png',
            self::IMG_SEARCH                => '16x16/search.png',
            self::IMG_DATE_PICKER           => '16x16/datepicker.png',
            self::IMG_TIME_PICKER           => '16x16/timepicker.png',
            self::IMG_DTU                   => '16x16/admin_dtu.png',
        );
        
        $strImg = '';
        if (isset($aImage[$iImage])) {
            $strImg = $strPath . $aImage[$iImage];
        }
        return $strImg;
    }
    
    /**
     * Parse a given style attribute into the components it contains.
     * @param string $strStyle
     * @return array
     */
    static public function parseStyle($strStyle) : array
    {
        $aStyle = array();
        $aStyles = explode(';', trim($strStyle));
        foreach ($aStyles as $strStyleDef) {
            $aStyleDef = explode(':', trim($strStyleDef));
            if (count($aStyleDef) == 2) {
                $strName = trim($aStyleDef[0]);
                $strValue = trim($aStyleDef[1]);
                $strValue = rtrim($strValue, ';');
                $aStyle[$strName] = $strValue;
            }
        }
        return $aStyle;
    }
    
}

