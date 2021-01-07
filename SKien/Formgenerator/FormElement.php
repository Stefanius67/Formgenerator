<?php
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
class FormElement
{
    /** text-align:left (default)   */
    const   ALIGN_LEFT              = 0x000000;
    /** mandatory field. added to mandatory-list for JS validation    */
    const   MANDATORY               = 0x000001;
    /** hidden field    */
    const   HIDDEN                  = 0x000002;
    /** readonly field  */
    const   READ_ONLY               = 0x000004;
    /** text-align:right    */
    const   ALIGN_RIGHT             = 0x000008;
    /** field is extended with DTU (Date-Time-User) Button  */
    const   ADD_DTU                 = 0x000010;
    /** field is extendet with selectbutton (click calls javascript function OnSelect(strField) with fieldname    */
    const   ADD_SELBTN              = 0x000020;
    /** static field is displayed as hint (smaller font, darkgrey)  */
    const   HINT                    = 0x000040;
    /** static field is displayed as error (red text)   */
    const   ERROR                   = 0x000080;
    /** button with selectlist  */
    const   SELECT_BTN              = 0x000100;
    /** field is disabled   */
    const   DISABLED                = 0x000400;
    /** display static field as info (see stylesheet: .info)    */
    const   INFO                    = 0x000800;
    /** text-align:center   */
    const   ALIGN_CENTER            = 0x001000;
    /** field is extended with Cal-Button for datepicker    */
    const   ADD_DATE_PICKER         = 0x002000;
    /** field is extended with Clock-Button for timepicker  */
    const   ADD_TIME_PICKER         = 0x004000;
    /** suppress zero-values    */
    const   NO_ZERO                 = 0x008000;
    /** suppress zero-values    */
    const   PASSWORD                = 0x010000;
    /** file input  */
    const   FILE                    = 0x020000;
    /** add EUR - Suffix    */
    const   ADD_EUR                 = 0x040000;
    /** trim content (leading / trailing blanks)    */
    const   TRIM                    = 0x080000;
    /** field invokes color picker on click */
    const   ADD_COLOR_PICKER        = 0x0100000;
    /** set data for CKEdit through JS  */
    const   SET_JSON_DATA           = 0x0200000;
    /** font-weight: bold   */
    const   BOLD                    = 0x0400000;
    /** replace '<br/>' / '<br> with CR */
    const   REPLACE_BR_CR           = 0x0800000;

    // TODO: Find a more general way of defining standard images. (possibly via a config)
    /** standard delete image */
    const IMG_DELETE            =  1;
    /** standard delete image */
    const IMG_SEARCH            =  2;
    /** standard image for date picker */
    const IMG_DATE_PICKER       =  3;
    /** standard image for time picker */
    const IMG_TIME_PICKER       =  4;
    /** standard image for dtu insert (DTU: Date,Time,User) */
    const IMG_DTU               =  5;
    
    /** @var FormGenerator the FormGenerator this element belongs to     */
    protected ?FormGenerator $oFG = null;
    /** @var FormElement the parent element - only FormGenerator has no parent     */
    protected ?FormElement $oParent = null;
    /** @var array all direct child elements     */
    protected array $aChild = array();
    /** @var array the width information for the cols inside this element     */
    protected ?array $aColWidth = null;
    /** @var string dimension of the width values ('%', 'px', 'em')     */
    protected string $strWidthDim = '%';
    /** @var int tab position of the element if it can get focus     */
    protected int $iTab = -1;
    /** @var int col inside current line     */
    protected int $iCol = 0;
    /** @var string element name     */
    protected string $strName = '';
    /** @var string element id     */
    protected string $strID = '';
    /** @var string CSS class of the element     */
    protected string $strClass = '';
    /** @var string validation for the element     */
    protected string $strValidate = '';
    /** @var int flags that specify the appearance and behaviour     */
    protected int $wFlags = 0;
    /** @var array attributes of the element     */
    protected ?array $aAttrib = null;
    /** @var array (CSS) styles of the element     */
    protected ?array $aStyle = null;
    /** @var bool set to true, if element creates some JS     */
    protected bool $bCreateScript = false;
    /** @var bool set to true, if element creates some CSS style     */
    protected bool $bCreateStyle = false;

    /**
     * nothing to initialize so far.
     * Keep it in code because derived classes call the parent constructor. 
     */
    public function __construct()
    {
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
     * Add a child to this element.
     * @param FormElement $oElement
     * @return FormElement
     */
    public function add(FormElement $oElement) : FormElement
    {
        $oElement->setParent($this);
        $this->aChild[] = $oElement;
        if ($this->oFG !== null ) {
            $this->oFG->addElement($oElement);
        } else {
            trigger_error('No FormGenerator object set!', E_USER_ERROR);
        }
        
        return $oElement;
    }

    /**
     * Set the parent of this element.
     * The Formgenerator of the parent is adopted for this element. 
     * If there are global flags set for the FormGenerator, this flags are added.
     * @param FormElement $oParent
     */
    public function setParent(FormElement $oParent) : void 
    {
        $this->oParent = $oParent;
        $this->oFG = $oParent->oFG;
        if ($this->oFG !== null ) {
            $this->addFlags($this->oFG->getGlobalFlags());
        } else {
            trigger_error('No FormGenerator object set!', E_USER_ERROR);
        }
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
     * Set width for the cols included in this element.
     * @param array $aColWidth
     * @param string $strDim
     */
    public function setColWidth(array $aColWidth, string $strDim='%') : void 
    {
        $this->aColWidth = $aColWidth;
        $this->strWidthDim = $strDim;
    }
    
    /**
     * Get colwidth for requested element.
     * If no width set, we try to get the width throught the parent.
     * @param int $iCol  requested col, if -1 current col is used
     * @return string       colwidth including dimension
     */
    public function getColWidth(int $iCol=-1) : string 
    {
        $strWidth = '';
        if ($iCol < 0) {
            $iCol = $this->iCol;
        }
        if ($this->aColWidth != null && $iCol < count($this->aColWidth) && $this->aColWidth[$iCol] >= 0) {
            $strWidth = $this->aColWidth[$iCol] . $this->strWidthDim;
        } else if ($this->oParent != null) {
            $strWidth = $this->oParent->getColWidth($iCol);
        }
        return $strWidth;
    }

    /**
     * Add a new div as child.
     * @param int $iWidth   width of the div in percent
     * @param int $iAlign   align (FormDiv::NONE, FormDiv::LEFT, FormDiv::RIGHT, FormDiv::CLEAR)
     * @param string $strID ID of the div
     * @return \SKien\Formgenerator\FormDiv created div element
     */
    public function addDiv(int $iWidth = 0, int $iAlign = FormDiv::CLEAR, string $strID = '') : FormDiv
    {
        $oDiv = new FormDiv($iWidth, $iAlign);
        $oDiv->SetID($strID);
        $this->add($oDiv);
    
        return $oDiv;
    }
    
    /**
     * Add a new fieldset to the element.
     * @param string $strLegend text or image of the legend
     * @param string $strID
     * @param int $iType type of the legend (FormFieldSet::TEXT or FormFieldSet::IMAGE)
     * @return \SKien\Formgenerator\FormFieldSet
     */
    public function addFieldSet(string $strLegend, string $strID = '', $iType = FormFieldSet::TEXT) : FormFieldSet
    {
        $oFS = new FormFieldSet($strLegend, $strID, $iType);
        $this->add($oFS);
    
        return $oFS;
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
     * Set the tab number of the element.
     * Element can save given tab number or return false if it has no tabstopp.
     * Method is called from the PageGenerator after an element is added to the form.
     * @param int $iTab
     */
    public function setTab(int $iTab) : void
    {
        $this->iTab = $iTab;
    }

    /**
     * Check, if the element has tab stop.
     * Derived classes have to decide by its own, if a tab stop is needed. Only if
     * this method returns true, the PageGenerator call the setTab() method with
     * the next available tab position.
     * @return bool
     */
    public function hasTab() : bool
    {
        return false;
    }
    
    /**
     * Add flags to element. 
     * @param int $wFlags
     */
    public function addFlags(int $wFlags) : void 
    {
        $this->wFlags |= $wFlags;
    }
    
    /**
     * Add any attribute.
     * If the attribute allready exist, the value will be overwritten.
     * @param string $strName
     * @param string $strValue
     */
    public function addAttribute(string $strName, string $strValue='') : void 
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
     * Build the HTML-notation for the element and/or all child elements.
     * @return string
     */
    public function getHTML() : string
    {
        $strHTML = '';
        $iCnt = count($this->aChild);
        for ($i = 0; $i < $iCnt; $i++) {
            $strHTML .= $this->aChild[$i]->GetHTML();
        }
        return $strHTML;
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
     * Build the 'container' div arround the current element.
     * @return string
     */
    protected function buildContainerDiv() : string
    {
        $strWidth = $this->getColWidth();
        $strHTML  = '       ';
        $strHTML .= '<div style="float: left;';
        if (!empty($strWidth)) {
            $strHTML .= ' width: ' . $strWidth . ';';
        }
        $strHTML .= '">';

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
                if (strlen($strValue) > 0)
                    $strAttrib .= '="' . $strValue . '"';
            }
        }
        return $strAttrib;
    }
    
    /**
     * Build the markup for the value attribute.
     * @param mixed $value
     * @return string Empty string if no value set, complete attribute if set
     */
    protected function buildValue($value) : string
    {
        $strValue = '';
        if (!empty($value)) {
            $strValue = ' value="' . $value . '"';
        }
        return $strValue;
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
     * @param int $iTab
     * @return string Empty string if $iTab = 0, complete attribute if set
     */
    protected function buildTab(int $iTab) : string
    {
        $strTab = '';
        if ($iTab > 0) {
            $strTab = ' tabindex="' . $iTab . '"';
        }
        return $strTab;
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

