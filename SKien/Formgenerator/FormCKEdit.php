<?php
namespace SKien\Formgenerator;

/**
 * HTML input using CKEditor
 *
 * history:
 * date         version
 * 2020-05-12   initial version
 *
 * @package Formgenerator
 * @version 1.0.0
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormCKEdit extends FormTextArea
{
    /** 'Source' - Button   */
    const   TB_SOURCE           = 0x00000002;
    /** Basic Styles:  Bold, Italic, Underline, Subscript, Superscript, RemoveFormat    */
    const   TB_BASIC_STYLES     = 0x00000004;
    /** Paragraph Formation: NumberedList, BulletedList, Outdent, Indent, JustifyLeft, -Center, -Right' */
    const   TB_PARAGRAPH        = 0x00000008;
    /** Links: link, Unlink */
    const   TB_LINKS            = 0x00000010;
    /** Insert Image    */
    const   TB_IMAGE            = 0x00000020;
    /** Colors: Text-, Backgroundcolor  */
    const   TB_COLOR            = 0x00000040;
    /** Table funtions insert and delete rows   */
    /* not working so far !!
     const  tbTable             = 0x00000080; */
    /** SelectBox for defined Styles    */
    const   TB_STYLES_SELECT    = 0x00000100;
    /** Select predefined Templates */
    const   TB_TEMPLATES        = 0x00000200;
    /** SelectBox for Placeholders  */
    const   TB_PLACEHOLDERS     = 0x00000400;
    /** Insert Codesnippet  */
    const   TB_SNIPPET          = 0x00000800;
    
    /** small toolbar (only basic styles)   */
    const   TB_SMALL    = 0x00000004; // self::TB_BASIC_STYLES;
    /** toolbar for content edit (no colors, templates and snippets)   */
    const   TB_CONTENT  = 0xfffff5bd; // 0xffffffff & ~( self::TB_COLOR | self::TB_TEMPLATES | self::TB_SNIPPET | TB_SOURCE);
    /** full toolbar (no templates)   */
    const   TB_FULL     = 0xfffffdfd; // 0xffffffff & ~(self::TB_TEMPLATES | TB_SOURCE);
    
    /** @var string the CSS file used inside the editarea    */
    protected string $strContentsCss;
    /** @var string the id of the editarea   */
    protected string $strBodyID;
    /** @var string the content for the editor as JSON   */
    protected ?string $strJsonData = null;
    /** @var array custom button definition ["func" => <buttonhandler>, "name" => <buttonname>]    */
    protected ?array $aCustomBtn = null;
    /** @var string allowed content    */
    protected ?string $strAllowedContent;
    /** @var int toolbar mask    */
    protected int $lToolbar;
    
    /**
     * Creates a WYSIWYG editor.
     * @param string $strName
     * @param string $strValue
     * @param int $iRows
     * @param string $strWidth
     * @param int $wFlags
     */
    public function __construct(string $strName, string $strValue, int $iRows, string $strWidth = '95%', int $wFlags = 0) 
    {
        if (($wFlags & FormInput::SET_JSON_DATA) != 0) {
            $this->strJsonData = $strValue;
            $strValue = '';
        }
        // add 2 rows to increase height for toolbar
        parent::__construct($strName, $strValue, 0, $iRows + 2, $strWidth, $wFlags);
        $this->bCreateScript = true;
        $this->bCreateStyle = true;
        $this->strContentsCss = '../style/ckeditarea.css';
        $this->strBodyID = 'editarea';
        $this->strAllowedContent = null;
        $this->lToolbar = self::TB_CONTENT;
    } 

    /**
     * Set the CSS file to use in the edit area.
     * @param string $strContentsCss
     */
    public function setContentsCss(string $strContentsCss) : void
    {
        $this->strContentsCss = $strContentsCss;
    }

    /**
     * Set the ID of the body.
     * This is the ID of the 'Container' element in which the text to be edited here 
     * should be displayed. This ID is required so that the correct CSS selectors are 
     * used for the display here in the editor. 
     * @param string $strBodyID
     */
    public function setBodyID(string $strBodyID) : void 
    {
        $this->strBodyID = $strBodyID;
    }
    
    /**
     * Add custom button to the beginning of the toolbar.
     * @param string $strName       Name (Text) of the Button
     * @param string $strFunction   JS-Function to handle click (func gets editor as paramter)
     */
    public function addCustomButton(string $strName, string $strFunction) : void 
    {
        if (!is_array($this->aCustomBtn)) {
            $this->aCustomBtn = array();
        }
        
        $this->aCustomBtn[] = array('func' => $strFunction, 'name' => $strName);
    }
    
    /**
     * Specify allowed content (see documentation of CKEdit for details)
     * @param string $strAllowedContent leave empty to allow everything (default)  
     */
    public function setAllowedContent(string $strAllowedContent = '') : void
    {
        $this->strAllowedContent = $strAllowedContent;
    }
    
    /**
     * Build the JS script to Load the editor:
     * - contentCSS
     * - bodyID
     * - Toolbar
     * - Custom Buttons
     */
    public function getScript() : string 
    {
        $aToolbar = array();;
        if (is_array($this->aCustomBtn) && count($this->aCustomBtn) > 0) {
            reset($this->aCustomBtn);
            foreach ($this->aCustomBtn as $aBtn) {
                $aToolbar[] = array('items' => array($aBtn['func']));
            }
        }
        $aToolbar = array_merge($aToolbar, $this->getToolbarDef());
        
        // just everything allowed ...
        $strAllowedContent = "true";
        if (strlen($this->strAllowedContent) > 0) {
            $strAllowedContent = "'" . $this->strAllowedContent . "'";
        }
        
        $strScript  = "var editor = null;" . PHP_EOL;
        $strScript .= "function LoadEditor() {" . PHP_EOL;
        $strScript .= "    // get initial size of textarea to replace" . PHP_EOL;
        $strScript .= "    var oTA = document.getElementById('" . $this->strName . "');" . PHP_EOL;
        $strScript .= "    var iHeight = 80;" . PHP_EOL;
        $strScript .= "    if (oTA) {" . PHP_EOL;
        $strScript .= "        iHeight = oTA.offsetHeight;" . PHP_EOL;
        $strScript .= "        iWidth = oTA.offsetWidth;" . PHP_EOL;
        $strScript .= "    }" . PHP_EOL;
        $strScript .= "    // create and Initialize HTML-Editor" . PHP_EOL;
        $strScript .= "    editor = CKEDITOR.replace('" . $this->strName . "' , {" . PHP_EOL;
        $strScript .= "            contentsCss : '" . $this->strContentsCss . "'," . PHP_EOL;
        $strScript .= "            bodyId : '" . $this->strBodyID . "'," . PHP_EOL;
        $strScript .= "            removePlugins : 'elementspath'," . PHP_EOL;
        $strScript .= "            toolbar: " . json_encode($aToolbar) . ", ";
        $strScript .= "            toolbarCanCollapse : false," . PHP_EOL;
        $strScript .= "            pasteFilter : 'plain-text'," . PHP_EOL;
        $strScript .= "            allowedContent : " . $strAllowedContent . "," . PHP_EOL;
        $strScript .= "            resize_enabled : false" . PHP_EOL;
        $strScript .= "        });" . PHP_EOL;
        
        // commands for custom buttons
        if (is_array($this->aCustomBtn) && count($this->aCustomBtn) > 0) {
            reset($this->aCustomBtn);
            foreach ($this->aCustomBtn as $aBtn) {
                $strButton = $aBtn['func'];
                $strCommand = 'cmd_' . $strButton;
                $strName = $aBtn['name'];
                $strScript .= "    editor.addCommand('" . $strCommand . "', { exec: function(editor) { " . $strButton . "(editor);  } });" . PHP_EOL;
                $strScript .= "    editor.ui.addButton('" . $strButton . "', { label: '" . $strName . "', command: '" . $strCommand . "' });" . PHP_EOL;
            }
        }
        // resize to desired size
        $strScript .= PHP_EOL;
        $strScript .= "    CKEDITOR.on('instanceReady', function(event) {" . PHP_EOL;
        $strScript .= "            editor.resize(iWidth, iHeight);" . PHP_EOL;
        $strScript .= "        });" . PHP_EOL;
        
        // if data to edit provided in JSON format, set it
        if (($this->wFlags & self::SET_JSON_DATA) != 0 && $this->strJsonData != '') {
            $strScript .= PHP_EOL;
            $strScript .= '    editor.setData(' . json_encode($this->strJsonData, JSON_PRETTY_PRINT) . ');' . PHP_EOL;
        }       
        $strScript .= "}" . PHP_EOL;
        
        return $strScript;
    }
    
    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::getStyle()
     */
    public function getStyle() : string 
    {
        $strStyle  = '';
        
        if (is_array($this->aCustomBtn) && count($this->aCustomBtn) > 0) {
            reset($this->aCustomBtn);
            foreach ($this->aCustomBtn as $aBtn) {
                $strBtn = strtolower($aBtn['func']);
                $strStyle .= '.cke_button__' . $strBtn . '_icon { display: none !important; }' . PHP_EOL;
                $strStyle .= '.cke_button__' . $strBtn . '_label { display: inline !important; }' . PHP_EOL;
            }
        }
        
        return $strStyle;
    }
    
    /**
     * Define toolbar to display.
     * @param int $lToolbar
     */
    public function setToolbar(int $lToolbar) : void
    {
        $this->lToolbar = $lToolbar;
    }
    
    /**
     * Returns currently defined toolbar.
     * @return int
     */
    public function getToolbar() : int
    {
        return $this->lToolbar;
    }
    
    /**
     * Returns currently defined toolbar as array for JSON-encoding.
     * @return array
     */
    public function getToolbarDef() : array
    {
        $aToolbar = array();
    
        if (($this->lToolbar & self::TB_BASIC_STYLES) != 0)
            $aToolbar[] = array('name' => 'basicstyles',   'items' => array('Bold','Italic','Underline','Subscript','Superscript','-','RemoveFormat'));
        if (($this->lToolbar & self::TB_PARAGRAPH) != 0)
            $aToolbar[] = array('name' => 'paragraph',     'items' => array('NumberedList','BulletedList','-','Outdent', 'Indent','-','JustifyLeft','JustifyCenter','JustifyRight'));
        if (($this->lToolbar & self::TB_LINKS) != 0)
            $aToolbar[] = array('name' => 'links',         'items' => array('Link','Unlink'));
        if (($this->lToolbar & (self::TB_IMAGE | self::TB_SNIPPET)) != 0) {
            $aInsert = array();
            if (($this->lToolbar & self::TB_IMAGE) != 0)
                $aInsert[] = 'Image';
            if (($this->lToolbar & self::TB_SNIPPET) != 0)
                $aInsert[] = 'CodeSnippet';
            $aToolbar[] = array('name' => 'insert',        'items' => $aInsert);
        }
        if (($this->lToolbar & self::TB_COLOR) != 0)
            $aToolbar[] = array('name' => 'color',         'items' => array('TextColor','BGColor'));
        if (($this->lToolbar & self::TB_STYLES_SELECT) != 0)
            $aToolbar[] = array('items' => array('Styles'));
        if (($this->lToolbar & self::TB_TEMPLATES) != 0)
            $aToolbar[] = array('items' => array('Templates'));
        if (($this->lToolbar & self::TB_PLACEHOLDERS) != 0)
            $aToolbar[] = array('items' => array('placeholder_select'));
        if (($this->lToolbar & self::TB_SOURCE) != 0)
            $aToolbar[] = array('name' => 'document',      'items' => array('Source'));
    
        return $aToolbar;
    }
}
