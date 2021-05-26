<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

use SKien\Config\ConfigInterface;
use SKien\Config\NullConfig;

/**
 * Container for all form elements.
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormGenerator extends FormCollection
{
    /** @var int last tab position in the form   */
    protected int $iLastTabindex = 0;
    /** @var bool set the whole form to readonly  */
    protected bool $bReadOnly = false;
    /** @var bool set debug mode to output more information  */
    protected bool $bDebugMode = false;
    /** @var bool set dialog mode (form runs in dynamic created iframe)  */
    protected bool $bIsDialog = false;
    /** @var int explicit width of the form in pixel  */
    protected int $iWidth = -1;
    /** @var FormFlags global flags for all form elements  */
    protected FormFlags $oGlobalFlags;
    /** @var string JS function for onsubmit  */
    protected string $strOnSubmit = '';
    /** @var string JS function for oncancel  */
    protected string $strOnCancel = '';
    /** @var array config values to pass as object to JS */
    protected array $aConfigForJS = [];
    /** @var string path to the images  */
    protected string $strImgPath = '';
    /** @var string URL params for the form action  */
    protected string $strAction;
    /** @var string form target  */
    protected string $strFormTarget = '';
    /** @var FormDataInterface data provider     */
    protected FormDataInterface $oData;
    /** @var ConfigInterface configuration     */
    protected ConfigInterface $oConfig;

    /**
     * Create a FormGenerator.
     * Set some values to default.
     * @param FormDataInterface $oData
     * @param string $strID
     */
    public function __construct(?FormDataInterface $oData, string $strID = 'FormGenerator')
    {
        $this->oFG = $this;
        $this->oData = $oData ?? new NullFormData();
        $this->oConfig = new NullConfig();
        $this->oGlobalFlags = new FormFlags();
        $this->strID = $strID;
        $this->strOnSubmit = "validateForm();";
        $this->strOnCancel = "javascript:history.back();";
        $this->strAction = $_SERVER['PHP_SELF'];
        if (isset($_SERVER['QUERY_STRING'])) {
            $this->strAction .= '?' . $_SERVER['QUERY_STRING'];
        }
        $this->bDebugMode = (error_reporting() & (E_USER_ERROR | E_USER_WARNING)) != 0;
    }

    /**
     * <big><b>!!!  The FormGenerator itself can't be created direct through the fromXML() - method !!!</b></big>
     * However, this method must be implemented because it is declared as abstract in the higher-level class
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     * @codeCoverageIgnore
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        return null;
    }

    /**
     * @return \SKien\Formgenerator\FormDataInterface
     */
    public function getData() : FormDataInterface
    {
        return $this->oData;
    }

    /**
     * @return \SKien\Config\ConfigInterface
     */
    public function getConfig() : ConfigInterface
    {
        return $this->oConfig;
    }

    /**
     * @param \SKien\Formgenerator\FormDataInterface $oData
     */
    public function setData(FormDataInterface $oData) : void
    {
        $this->oData = $oData;
    }

    /**
     * @param \SKien\Config\ConfigInterface $oConfig
     */
    public function setConfig(ConfigInterface $oConfig) : void
    {
        $this->oConfig = $oConfig;
    }

    /**
     * Set the action executed when submit the form.
     * If not changed here, <i><b>$_SERVER['QUERY_STRING']</b></i> is used.
     * @param string $strAction (default = '')
     */
    public function setAction(string $strAction) : void
    {
        $this->strAction = $strAction;
    }

    /**
     * Set the path to images.
     * @param string $strPath
     */
    public function setImagePath(string $strPath) : void
    {
        $this->strImgPath = $strPath;
    }

    /**
     * Get the path to the StdImages  (<b>WITHOUT trailing DIRECTORY_SEPARATOR!</b>).
     * If no directory set, the subdirectory 'StdImages' of this sourcefile is used.
     * @return string current image path
     */
    public function getImagePath() : string
    {
        if ($this->strImgPath == '') {
            $this->strImgPath = str_replace(rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR), '', __DIR__);
            $this->strImgPath .= DIRECTORY_SEPARATOR . 'StdImages';
            $this->strImgPath = $this->getConfig()->getString('Images.Path', $this->strImgPath);
        }
        return rtrim($this->strImgPath, DIRECTORY_SEPARATOR);
    }

    /**
     * Get filename for predefined standard images
     * @param int $iImage
     * @return array
     */
    public function getStdImage(int $iImage) : array
    {
        $aImageMap = [
            FormImage::IMG_DELETE      => ['Delete', 'delete.png', 'Delete content'],
            FormImage::IMG_SEARCH      => ['Search', 'search.png', 'Select'],
            FormImage::IMG_BROWSE      => ['Browse', 'browse.png', 'Browse on server'],
            FormImage::IMG_DATE_PICKER => ['DatePicker', 'datepicker.png', 'Select date'],
            FormImage::IMG_TIME_PICKER => ['TimePicker', 'timepicker.png', 'Select time'],
            FormImage::IMG_DTU         => ['InsertDTU', 'insert_dtu.png', 'insert current date, time and user'],
        ];

        $strImage = '';
        $strTitle = '';
        if (isset($aImageMap[$iImage])) {
            $aImage = $aImageMap[$iImage];
            $strImage = $this->getImagePath() . DIRECTORY_SEPARATOR;
            $strImage .= $this->getConfig()->getString('Images.StdImages.' . $aImage[0] . '.Image', $aImage[1]);
            $strTitle = $this->getConfig()->getString('Images.StdImages.' . $aImage[0] . '.Text', $aImage[2]);
        }

        return [$strImage, $strTitle];
    }

    /**
     * Get the global flags.
     * @return int
     */
    public function getGlobalFlags() : int
    {
        return $this->oGlobalFlags->getFlags();
    }

    /**
     * Set the target
     * @param string $strFormTarget
     */
    public function setTarget(string $strFormTarget) : void
    {
        $this->strFormTarget = $strFormTarget;
    }

    /**
     * Set explicit width of the form.
     * @param int $iWidth    width (-1 for default)
     */
    public function setFormWidth(int $iWidth) : void
    {
        $this->iWidth = $iWidth;
    }

    /**
     * Sets the whole form to readonly.
     * All elements of the form set to disabled, Save/Cancel-Buttons are replaced
     * with Close-Button
     * @param bool $bReadOnly
     */
    public function setReadOnly(bool $bReadOnly) : void
    {
        $this->bReadOnly = $bReadOnly;
        if ($bReadOnly) {
            $this->oGlobalFlags->add(FormFlags::READ_ONLY);
        } else {
            $this->oGlobalFlags->remove(FormFlags::READ_ONLY);
        }
    }

    /**
     * Adjust the height of the two specified columns.
     * If we have two columns containing fieldset, the height pf both (... and the border)
     * usually differs dependent on the contents. To prettify the output, the height of the
     * smaller columns is set to the height of the bigger one.
     * @param string $strCol1
     * @param string $strCol2
     */
    public function adjustColHeight(string $strCol1, string $strCol2) : void
    {
        $strScript = "window.addEventListener('load', function () {";
        $strScript .= "adjustColumnHeight('" . $strCol1 . "', '" . $strCol2 . "');});";
        $this->add(new FormScript($strScript));
    }

    /**
     * Sets the debug mode to output more information.
     * Especialy for some elements, that need additional JS it can be helpfull to
     * turn on the debug mode.
     * @param bool $bDebugMode
     */
    public function setDebugMode(bool $bDebugMode) : void
    {
        // Even if we plan to integrate a logger, we keep the debug mode alive because the
        // logger do not cover the JS side (missing scripts, ...) and developers often
        // forget to take a look at the Browser console.
        $this->bDebugMode = $bDebugMode;
    }

    /**
     * Get state of the debug mode.
     * @return bool
     */
    public function getDebugMode() : bool
    {
        return $this->bDebugMode;
    }

    /**
     * Specify the form as an 'Dialog'.
     * Dialog means, the form runs inside of an dynamic iframe created in the
     * dialog - DIV of the parent. <br/>
     * The cancel can easy be done by clear the content of that dialog-DIV.
     */
    public function setDialog(bool $bDialog) : void
    {
        $this->bIsDialog = $bDialog;
    }

    /**
     * Check, if form runs in dialog (dynamic created iframe).
     * @return bool
     */
    public function isDialog() : bool
    {
        return $this->bIsDialog;
    }

    /**
     * Add any element to the form.
     * @param FormElementInterface $oElement
     */
    public function addElement(FormElementInterface $oElement) : void
    {
        $this->iLastTabindex += $oElement->setTabindex($this->iLastTabindex + 1);
    }

    /**
     * Add a key to the config passed to JS.
     * @param string $strKey
     * @param mixed $value
     */
    public function addConfigForJS(string $strKey, $value) : void
    {
        $this->aConfigForJS[$strKey] = $value;
    }

    /**
     * Create HTML for defined form.
     * @return string
     */
    public function getForm() : string
    {
        $strHTML = PHP_EOL;

        $strHTML .= '<form';
        if (!empty($this->strAction)) {
            $strHTML .= ' action="';
            $strHTML .= $this->strAction . '"';
        }
        if (!empty($this->strFormTarget)) {
            $strHTML .= ' target="' . $this->strFormTarget . '"';
        }
        if ($this->aAttrib != null) {
            foreach ($this->aAttrib as $strName => $strValue) {
                $strHTML .= ' ' . $strName . '="' . $strValue . '"';
            }
        }
        $strHTML .= $this->buildStyle();
        $strHTML .= ' method="post" id=' . $this->strID . ' onsubmit="return ' . $this->strOnSubmit . '">' . PHP_EOL;
        $strHTML .= '<div id=errorPopup onclick="this.style.display = \'none\';"></div>' . PHP_EOL;
        $iCnt = count($this->aChild);
        for ($i = 0; $i < $iCnt; $i++) {
            $strHTML .= $this->aChild[$i]->GetHTML();
        }

        $strHTML .= '</form>' . PHP_EOL;
        $strHTML .= PHP_EOL;

        return $strHTML;
    }

    /**
     * Build needed configuration for JS scripts.
     * - for validation
     * - scripts of all childs that have own script
     * @return string
     */
    public function getScript() : string
    {
        // set some general configurations
        $this->aConfigForJS['DebugMode'] = $this->getDebugMode();
        $this->aConfigForJS['FormDataValidation'] = $this->oConfig->getArray('FormDataValidation');
        $this->aConfigForJS['FormDataValidation']['formID'] = $this->strID;
        $this->aConfigForJS['JavaScript'] = $this->oConfig->getArray('JavaScript');

        $strScript = 'var g_oConfigFromPHP = ' . json_encode($this->aConfigForJS) . ';';
        return $strScript;
    }

    /**
     * Build needed CSS styles.
     * - body, if form width defined
     * - styles of all childs that have own styles
     * @return string
     */
    public function getStyle() : string
    {
        $strStyle = '';
        if ($this->iWidth > 0) {
            $strStyle = "body { width: " . $this->iWidth . "px;}" . PHP_EOL;
        }
        $iCnt = count($this->aChild);
        for ($i = 0; $i < $iCnt; $i++) {
            $strStyle .= $this->aChild[$i]->getStyle();
        }
        return $strStyle;
    }
}
