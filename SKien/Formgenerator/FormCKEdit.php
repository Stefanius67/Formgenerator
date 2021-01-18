<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * WYSIWYG - HTML input using CKEditor.
 * currently used/supportet CKEditor Version is 4.6.2
 * 
 * TODO: check, if it make sense to migrate to CKeditor 5 or 'only' update to newest 4.X 
 * @link https://ckeditor.com/docs/ckeditor5/latest/builds/guides/migrate.html 
 *
 * #### History
 * - *2020-05-12*   initial version
 * - *2021-01-07*   PHP 7.4
 * - *2021-01-09*   added support of icons for custom buttons and table, special char and iframe - Button
 *
 * @package Formgenerator
 * @version 1.1.0
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormCKEdit extends FormTextArea
{
    /** 'Source' - Button   */
    public const TB_SOURCE           = 0x00000002;
    /** Basic Styles:  Bold, Italic, Underline, Subscript, Superscript, RemoveFormat    */
    public const TB_BASIC_STYLES     = 0x00000004;
    /** Paragraph Formation: NumberedList, BulletedList, Outdent, Indent, JustifyLeft, -Center, -Right' */
    public const TB_PARAGRAPH        = 0x00000008;
    /** Links: link, Unlink */
    public const TB_LINKS            = 0x00000010;
    /** Insert Image    */
    public const TB_IMAGE            = 0x00000020;
    /** Colors: Text-, Backgroundcolor  */
    public const TB_COLOR            = 0x00000040;
    /** Table funtions insert and delete rows   */
    public const TB_TABLE            = 0x00000080;
    /** SelectBox for defined Styles    */
    public const TB_STYLES_SELECT    = 0x00000100;
    /** Select predefined Templates */
    public const TB_TEMPLATES        = 0x00000200;
    /** SelectBox for Placeholders  */
    public const TB_PLACEHOLDERS     = 0x00000400;
    /** Insert Codesnippet  */
    public const TB_SNIPPET          = 0x00000800;
    /** Insert Special Chars  */
    public const TB_SPECIAL_CHAR     = 0x00001000;
    /** Insert Iframe  */
    public const TB_IFRAME           = 0x00002000;
    
    /** small toolbar (only basic styles)   */
    public const TB_SMALL    = 0x00000004; // TB_BASIC_STYLES;
    /** insert objects   */
    public const TB_INSERT   = 0x000038A0; // TB_IMAGE | TB_TABLE | TB_SNIPPET | TB_SPECIAL_CHAR | TB_IFRAME
    /** toolbar for content edit (no colors, templates and objects)   */
    public const TB_CONTENT  = 0xfffff53d; // 0xffffffff & ~(TB_COLOR | TB_TEMPLATES | TB_INSERT | TB_SOURCE);
    /** full toolbar (no templates)   */
    public const TB_FULL     = 0xfffffdfd; // 0xffffffff & ~(TB_TEMPLATES | TB_SOURCE);
    
    /** custom button only with text   */
    public const BTN_TEXT           = 0x01;
    /** custom button only with icon   */
    public const BTN_ICON           = 0x02;
    /** custom button with text and icon  */
    public const BTN_TEXT_ICON      = 0x03;
    
    /** @var string the CSS file used inside the editarea    */
    protected string $strContentsCss = '';
    /** @var string the id of the editarea   */
    protected string $strBodyID;
    /** @var array custom button definition ["func" => <buttonhandler>, "name" => <buttonname>]    */
    protected array $aCustomBtn = [];
    /** @var string allowed content    */
    protected string $strAllowedContent = '';
    /** @var int toolbar mask    */
    protected int $lToolbar;
    
    /**
     * Creates a WYSIWYG editor.
     * @param string $strName
     * @param int $iRows
     * @param string $strWidth
     * @param int $wFlags
     */
    public function __construct(string $strName, int $iRows, string $strWidth = '95%', int $wFlags = 0) 
    {
        // add 2 rows to increase height for toolbar
        parent::__construct($strName, 0, $iRows + 2, $strWidth, $wFlags);
        $this->bCreateScript = true;
        $this->bCreateStyle = true;
        $this->strBodyID = 'editarea';
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
     * If icon specified take care it the path is absolute or relative to the script that
     * containing this CKEditor.
     * @param string $strName       Name (Text) of the Button
     * @param string $strFunction   JS-Function to handle click (func gets editor as paramter)
     * @param string $strIcon       Icon for the button
     * @param int $iType            Type of the button (FormCKEdit::BTN_TEXT, FormCKEdit::BTN_ICON or FormCKEdit::BTN_TXET_ICON)
     */
    public function addCustomButton(string $strName, string $strFunction, string $strIcon = '', int $iType = self::BTN_TEXT) : void 
    {
        if (empty($strIcon)) {
            $iType = self::BTN_TEXT;
        }
        $this->aCustomBtn[] = [
            'func' => $strFunction, 
            'name' => $strName,
            'icon' => $strIcon,
            'type' => $iType,
        ];
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
        // define a global instance of the editor
        $strScript = "var editor = null;" . PHP_EOL;
        
        // this function must be called in the html.body.onload() event!
        $strScript .= 
            "function LoadEditor()" . PHP_EOL .
            "{" . PHP_EOL .
            "    // get initial size of textarea to replace" . PHP_EOL .
            "    var oTA = document.getElementById('" . $this->strName . "');" . PHP_EOL .
            "    var iHeight = 80;" . PHP_EOL .
            "    if (oTA) {" . PHP_EOL .
            "        iHeight = oTA.offsetHeight;" . PHP_EOL .
            "        iWidth = oTA.offsetWidth;" . PHP_EOL .
            "    }" . PHP_EOL;
        
        $strScript .= $this->buildEditorCreateScript();
        $strScript .= $this->buildCustomBtnScript();
        
        // resize to desired size
        $strScript .= "    CKEDITOR.on('instanceReady', function(event) {" . PHP_EOL;
        $strScript .= "            editor.resize(iWidth, iHeight);" . PHP_EOL;
        $strScript .= "        });" . PHP_EOL;
        
        // if data to edit provided in JSON format, set it
        // TODO: explain differences when using FormInput::SET_JSON_DATA
        if (($this->wFlags & self::SET_JSON_DATA) != 0) {
            $strJsonData = $this->oFG->oData->getValue($this->strName);
            if (strlen($strJsonData) > 0 ) {
                $strScript .= PHP_EOL;
                $strScript .= '    editor.setData(' . json_encode($strJsonData, JSON_PRETTY_PRINT) . ');' . PHP_EOL;
            }
        }       
        $strScript .= "}" . PHP_EOL;
        
        return $strScript;
    }
    
    /**
     * Build CKEditor specific styles.
     * @return string
     */
    public function getStyle() : string 
    {
        // If custom toolbar buttons defined, for each button dependent on the his
        // type (TEXT, ICON, TEXT+ICON) some styles have to be set.
        $strStyle = '';
        foreach ($this->aCustomBtn as $aBtn) {
            $strBtn = strtolower($aBtn['func']);
            $strDisplayLabel = (($aBtn['type'] & self::BTN_TEXT) != 0) ? 'inline' : 'none';
            $strDisplayIcon = (($aBtn['type'] & self::BTN_ICON) != 0) ? 'inline' : 'none';
            $strStyle .= '.cke_button__' . $strBtn . '_icon { display: ' . $strDisplayIcon . ' !important; }' . PHP_EOL;
            $strStyle .= '.cke_button__' . $strBtn . '_label { display: ' . $strDisplayLabel . ' !important; }' . PHP_EOL;
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
     * Build the script that creates the CKEditor instance.
     * @return string
     */
    protected function buildEditorCreateScript() : string
    {
        if (strlen($this->strContentsCss) == 0) {
            trigger_error('No CSS Stylesheet set!', E_USER_WARNING);
        }
        $aCKEditor = [
            'contentsCss' => $this->strContentsCss,
            'bodyId' => $this->strBodyID,
            'removePlugins' => 'elementspath',
            'toolbar' => $this->buildToolbarDef(),
            'toolbarCanCollapse' => false,
            'pasteFilter' => 'plain-text',
            'allowedContent' => ($this->strAllowedContent ?: true),
            'resize_enabled' => false,
        ];
        $strScript = '    editor = CKEDITOR.replace("' . $this->strName . '", ' . json_encode($aCKEditor) . ');';
        
        return $strScript;
    }
    
    /**
     * Build the script tp define all custom buttons.
     * @return string
     */
    protected function buildCustomBtnScript() : string
    {
        $strScript = '';
        // commands for custom buttons
        if (is_array($this->aCustomBtn) && count($this->aCustomBtn) > 0) {
            reset($this->aCustomBtn);
            foreach ($this->aCustomBtn as $aBtn) {
                $strScript .= $this->buildBtnScript($aBtn);
            }
        }
        return $strScript;
    }
    
    /**
     * Build the script to define a custom Button for the CFKEditor Toolbar.
     * @param array $aBtn
     * @return string
     */
    protected function buildBtnScript(array $aBtn) : string
    {
        $strCommand = 'cmd_' . $aBtn['func'];
        $aCommand = [
            'exec' => 'function(editor){' . $aBtn['func'] . '(editor);}',
        ];
        $aButton = [
            'label' => $aBtn['name'],
            'command' => $strCommand,
            'icon' => $aBtn['icon'],
        ];
        
        $strScript  = "    editor.addCommand('" . $strCommand . "', " . json_encode($aCommand) . ");" . PHP_EOL;
        $strScript .= "    editor.ui.addButton('" . $aBtn['func'] . "', " . json_encode($aButton) . ");" . PHP_EOL;
        
        return $strScript;
    }
    
    /**
     * Returns currently defined toolbar as array for JSON-encoding.
     * @link https://ckeditor.com/latest/samples/toolbarconfigurator/index.html
     * @return array
     */
    protected function buildToolbarDef() : array
    {
        $aToolbar = [];
        $this->addCustomBtns($aToolbar);
        $this->addBasicStyleBtns($aToolbar);
        $this->addParagraphBtns($aToolbar);
        $this->addLinkBtns($aToolbar);
        $this->addInsertBtns($aToolbar);
        $this->addColorBtns($aToolbar);
        $this->addStyleSelect($aToolbar);
        $this->addTemplateSelect($aToolbar);
        $this->addPlaceholderSelect($aToolbar);
        $this->addSourceBtn($aToolbar);
        
        return $aToolbar;
    }
    
    /**
     * Add all custom buttons at start of the toolbar.
     * @param array $aToolbar reference to the toolbar array
     */
    protected function addCustomBtns(array &$aToolbar) : void
    {
        foreach ($this->aCustomBtn as $aBtn) {
            $aToolbar[] = ['items' => [$aBtn['func']]];
        }
    }
    
    /**
     * Add button group for basic styles. 
     * @param array $aToolbar reference to the toolbar array
     */
    protected function addBasicStyleBtns(array &$aToolbar) : void
    {
        if (($this->lToolbar & self::TB_BASIC_STYLES) != 0) {
            $aToolbar[] = [
                'name' => 'basicstyles', 
                'items' => [
                    'Bold',
                    'Italic',
                    'Underline',
                    'Subscript',
                    'Superscript',
                    '-',
                    'RemoveFormat',
                ]
            ];
        }
    }
    
    /**
     * Add button group for paragraph formating. 
     * @param array $aToolbar reference to the toolbar array
     */
    protected function addParagraphBtns(array &$aToolbar) : void
    {
        if (($this->lToolbar & self::TB_PARAGRAPH) != 0) {
            $aToolbar[] = [
                'name' => 'paragraph',
                'items' => [
                    'NumberedList',
                    'BulletedList',
                    '-',
                    'Outdent', 
                    'Indent',
                    '-',
                    'JustifyLeft',
                    'JustifyCenter',
                    'JustifyRight',
                ]
            ];
        }
    }
    
    /**
     * Add button group for links. 
     * @param array $aToolbar reference to the toolbar array
     */
    protected function addLinkBtns(array &$aToolbar) : void
    {
        if (($this->lToolbar & self::TB_LINKS) != 0) {
            $aToolbar[] = [
                'name' => 'links',
                'items' => ['Link', 'Unlink']
            ];
        }
    }
    
    /**
     * Add button group to insert objects.
     * - Images
     * - Snippets
     * - Tables
     * - Special Chars
     * - IFrames 
     * @param array $aToolbar reference to the toolbar array
     */
    protected function addInsertBtns(array &$aToolbar) : void
    {
        if (($this->lToolbar & self::TB_INSERT) != 0) {
            $aInsert = array();
            if (($this->lToolbar & self::TB_IMAGE) != 0) {
                $aInsert[] = 'Image';
            }
            if (($this->lToolbar & self::TB_SNIPPET) != 0) {
                $aInsert[] = 'CodeSnippet';
            }
            if (($this->lToolbar & self::TB_TABLE) != 0) {
                $aInsert[] = 'Table';
            }
            if (($this->lToolbar & self::TB_SPECIAL_CHAR) != 0) {
                $aInsert[] = 'SpecialChar';
            }
            if (($this->lToolbar & self::TB_IFRAME) != 0) {
                $aInsert[] = 'Iframe';
            }
            $aToolbar[] = ['name' => 'insert', 'items' => $aInsert];
        }
    }
    
    /**
     * Add button group for colors 
     * @param array $aToolbar reference to the toolbar array
     */
    protected function addColorBtns(array &$aToolbar) : void
    {
        if (($this->lToolbar & self::TB_COLOR) != 0) {
            $aToolbar[] = ['name' => 'color', 'items' => ['TextColor', 'BGColor']];
        }
    }
    
    /**
     * Add select list for styles 
     * @param array $aToolbar reference to the toolbar array
     */
    protected function addStyleSelect(array &$aToolbar) : void
    {
        if (($this->lToolbar & self::TB_STYLES_SELECT) != 0) {
            $aToolbar[] = ['items' => ['Styles']];
        }
    }
    
    /**
     * Add select list for templates 
     * @param array $aToolbar reference to the toolbar array
     */
    protected function addTemplateSelect(array &$aToolbar) : void
    {
        if (($this->lToolbar & self::TB_TEMPLATES) != 0) {
            $aToolbar[] = ['items' => ['Templates']];
        }
    }
    
    /**
     * Add select list for placeholders 
     * @param array $aToolbar reference to the toolbar array
     */
    protected function addPlaceholderSelect(array &$aToolbar) : void
    {
        if (($this->lToolbar & self::TB_PLACEHOLDERS) != 0) {
            $aToolbar[] = ['items' => ['placeholder_select']];
        }
    }
    
    /**
     * Add button to switch in the source mode 
     * @param array $aToolbar reference to the toolbar array
     */
    protected function addSourceBtn(array &$aToolbar) : void
    {
        if (($this->lToolbar & self::TB_SOURCE) != 0) {
            $aToolbar[] = ['name' => 'document', 'items' => ['Source']];
        }
    }
}
