<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * WYSIWYG - HTML input using CKEditor.
 * uses CKEditor Version 4.15
 *
 * For more information about download, install and integration of the CKEditor, see
 * CKEditorIntegration.md
 *
 * To enable filebrowsing on the server for the insert mage and insert link functionality
 * the RichFilemanager is used. For more information see
 * RichFilemanager.md
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
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
    /** insert Table   */
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
    /** @var string initial folder to expand in filemanager for links   */
    protected string $strBrowseFolderLinkURL = '';
    /** @var string initial folder to expand in filemanager for images   */
    protected string $strBrowseFolderImageURL = '';
    /** @var string initial folder to expand in filemanager for image links   */
    protected string $strBrowseFolderImageLinkURL = '';

    /**
     * Creates a WYSIWYG editor.
     * @param string $strName
     * @param int $iRows
     * @param string $strWidth
     * @param int $wFlags
     */
    public function __construct(string $strName, int $iRows, string $strWidth = '100%', int $wFlags = 0)
    {
        // add 2 rows to increase height for toolbar
        parent::__construct($strName, 0, $iRows + 2, $strWidth, $wFlags);
        $this->strBodyID = 'editarea';
        $this->lToolbar = self::TB_CONTENT;
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        $strName = self::getAttribString($oXMLElement, 'name');
        $iRows = self::getAttribInt($oXMLElement, 'rows', 10);
        $strWidth = self::getAttribString($oXMLElement, 'width', '100%');
        $wFlags = self::getAttribFlags($oXMLElement);
        $oFormElement = new self($strName, $iRows, $strWidth, $wFlags);
        $oFormParent->add($oFormElement);
        $oFormElement->readAdditionalXML($oXMLElement);
        return $oFormElement;
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::readAdditionalXML()
     */
    public function readAdditionalXML(\DOMElement $oXMLElement) : void
    {
        parent::readAdditionalXML($oXMLElement);
        $this->setContentsCss(self::getAttribString($oXMLElement, 'content-css'));
        $this->setBodyID(self::getAttribString($oXMLElement, 'body-id'));
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
     * @param string $strBrowseFolderLinkURL
     */
    public function setBrowseFolderLinkURL(string $strBrowseFolderLinkURL) : void
    {
        $this->strBrowseFolderLinkURL = $strBrowseFolderLinkURL;
    }

    /**
     * @param string $strBrowseFolderImageURL
     */
    public function setBrowseFolderImageURL(string $strBrowseFolderImageURL) : void
    {
        $this->strBrowseFolderImageURL = $strBrowseFolderImageURL;
    }

    /**
     * @param string $strBrowseFolderImageLinkURL
     */
    public function setBrowseFolderImageLinkURL(string $strBrowseFolderImageLinkURL) : void
    {
        $this->strBrowseFolderImageLinkURL = $strBrowseFolderImageLinkURL;
    }

    /**
     * Load some configuratin after parent set.
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::onParentSet()
     */
    protected function onParentSet() : void
    {
        $aCKEditor = [
            'Path' => $this->oFG->getConfig()->getString('CKEditor.Path'),
            'editorID' => $this->strName,
            'editorOptions' => $this->buildEditorOptions(),
            'customButtons' => $this->aCustomBtn,
        ];
        // pass the content through the JSON data
        if ($this->oFlags->isSet(FormFlags::SET_JSON_DATA)) {
            $aCKEditor['editorData'] = $this->oFG->getData()->getValue($this->strName);
        }
        $this->oFG->addConfigForJS('CKEditor', $aCKEditor);

        $strRfmPath = $this->oFG->getConfig()->getString('RichFilemanager.Path');
        if ($strRfmPath != '') {
            if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strRfmPath)) {
                if ($this->oFG->getDebugMode()) {
                    trigger_error('Can not find Rich Filemanager at [' . $_SERVER['DOCUMENT_ROOT'] . $strRfmPath . ']', E_USER_WARNING);
                }
            }

            $strBrowseFolderLinkURL = $this->getBrowseFolder($this->strBrowseFolderLinkURL, 'RichFilemanager.expandFolder.browseLinkURL');
            $strBrowseFolderImageURL = $this->getBrowseFolder($this->strBrowseFolderImageURL, 'RichFilemanager.expandFolder.browseImageURL');
            $strBrowseFolderImageLinkURL = $this->getBrowseFolder($this->strBrowseFolderImageLinkURL, 'RichFilemanager.expandFolder.browseImageLinkURL');
            $aRFN = [
                'Path' => $strRfmPath,
                'language' => $this->oFG->getConfig()->getString('RichFilemanager.language'),
                'expandFolder' => [
                    'browseLinkURL' => $strBrowseFolderLinkURL,
                    'browseImageURL' => $strBrowseFolderImageURL,
                    'browseImageLinkURL' => $strBrowseFolderImageLinkURL ?: $strBrowseFolderLinkURL
                ]
            ];
            $this->oFG->addConfigForJS('RichFilemanager', $aRFN);
        }
    }

    /**
     * Get the startfolder for different purposes.
     * @param string $strFolder
     * @param string $strConfig
     * @return string
     */
    protected function getBrowseFolder(string $strFolder, string $strConfig) : string
    {
        if (strlen($strFolder) > 0) {
            return $strFolder;
        }
        return $this->oFG->getConfig()->getString($strConfig);
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

        if ($this->oFG->getConfig()->getString('RichFilemanager.Path')) {
            $strStyle .= PHP_EOL .
                ".fm-modal {" . PHP_EOL .
                "    z-index: 10011; /** Because CKEditor image dialog was at 10010 */" . PHP_EOL .
                "    width:80%;" . PHP_EOL .
                "    height:80%;" . PHP_EOL .
                "    top: 10%;" . PHP_EOL .
                "    left:10%;" . PHP_EOL .
                "    border:0;" . PHP_EOL .
                "    position:fixed;" . PHP_EOL .
                "}";
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
     * Build the options to create the CKEditor instance.
     * @return array
     */
    protected function buildEditorOptions() : array
    {
        if (strlen($this->strContentsCss) == 0) {
            trigger_error('No CSS Stylesheet set!', E_USER_WARNING);
        }
        $aCKEditor = [
            'contentsCss' => $this->strContentsCss,
            'skin' => $this->oFG->getConfig()->getString('CKEditor.skin', 'moonocolor'),
            'bodyId' => $this->strBodyID,
            'toolbar' => $this->buildToolbarDef(),
            'toolbarCanCollapse' => false,
            'uiColor' => $this->oFG->getConfig()->getString('CKEditor.uiColor', '#F8F8F8'),
            'pasteFilter' => $this->oFG->getConfig()->getString('CKEditor.pasteFilter', 'plain-text'),
            'colorButton_enableAutomatic' => $this->oFG->getConfig()->getBool('CKEditor.colorbutton.enableAutomatic', true),
            'colorButton_enableMore' => $this->oFG->getConfig()->getBool('CKEditor.colorbutton.enableMore', true),
            'allowedContent' => ($this->strAllowedContent ?: true),
            'resize_enabled' => false,
        ];
        $this->buildSelectableColors($aCKEditor);
        $this->buildPlaceholderSelect($aCKEditor);

        return $aCKEditor;
    }

    /**
     * Build config settings for the selectable colors.
     * @param array $aCKEditor
     */
    protected function buildSelectableColors(array &$aCKEditor) : void
    {
        $aSelectableColorsRaw = $this->oFG->getConfig()->getArray('CKEditor.colorbutton.selectableColors');
        $aSelectableColors = [];
        foreach ($aSelectableColorsRaw as $color) {
            $aSelectableColors[] = ltrim($color, '#');
        }
        if (($this->lToolbar & self::TB_COLOR) != 0 && count($aSelectableColors) > 0) {
            $strColors = '';
            $strSep = '';
            foreach ($aSelectableColors as $strColor) {
                $strColors .= $strSep . $strColor;
                $strSep = ',';
            }
            $aCKEditor['colorButton_colors'] = $strColors;
            $aCKEditor['colorButton_colorsPerRow'] = $this->oFG->getConfig()->getInt('CKEditor.colorbutton.colorsPerRow', 6);
        }
    }

    /**
     * Build config for available placeholders in the placeholder-combobox.
     * @param array $aCKEditor
     */
    protected function buildPlaceholderSelect(array &$aCKEditor) : void
    {
        $aPlaceholderselect = $this->oFG->getConfig()->getArray('CKEditor.placeholder');
        if (($this->lToolbar & self::TB_PLACEHOLDERS) != 0 && count($aPlaceholderselect) > 0) {
            $aCKEditor['placeholder_select'] = ['placeholders' => $aPlaceholderselect];
        }
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
        if (($this->lToolbar & self::TB_PLACEHOLDERS) != 0 && count($this->oFG->getConfig()->getArray('CKEditor.placeholder')) > 0) {
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
