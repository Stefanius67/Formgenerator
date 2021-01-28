<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

use SKien\Config\ConfigInterface;
use SKien\Config\NullConfig;

/**
 * Container for all form elements.
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
class FormGenerator extends FormContainer
{
    /** JS code to close dialog */
    const CMD_CLOSE_DLG = "parent.document.getElementById('dialog').innerHTML = '';";
    
    /** @var int last tab position in the form   */
    protected int $iLastTabindex = 0;
    /** @var bool set the whole form to readonly  */
    protected bool $bReadOnly = false;
    /** @var bool set debug mode to output more information  */
    protected bool $bDebugMode = false;
    /** @var string JS handler for onsubmit  */
    protected string $strOnSubmit = '';
    /** @var string JS handler for oncancel  */
    protected string $strOnCancel = '';
    /** @var int explicit width of the form in pixel  */
    protected int $iWidth = -1;
    /** @var FormFlags global flags for all form elements  */
    protected FormFlags $oGlobalFlags;
    /** @var array elements that creates dynamic script */
    protected array $aScriptElements = [];
    /** @var array elements that creates dynamic CSS styles */
    protected array $aStyleElements = [];
    /** @var string path to the images  */
    protected string $strImgPath;
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
        $this->strImgPath = '../images/';
        $this->strAction = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
        $this->bDebugMode = (error_reporting() & (E_USER_ERROR | E_USER_WARNING)) != 0; 
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
     * @return string current image path
     */
    public function getImagePath() : string
    {
        return $this->strImgPath;
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
    public function setDialog() : void
    {
        $this->setOnCancel(self::CMD_CLOSE_DLG);
    }
    
    /**
     * Change JS handler for form submit from default "ValidateForm();";<br/><br/>
     * Hide button, if set to empty string.<br/><br/>
     * <b>!! Attention !!</b>
     * don't use double quotes in function!
     * @param string $strOnSubmit
     */
    public function setOnSubmit(string $strOnSubmit) : void 
    {
        $this->strOnSubmit = $strOnSubmit;
    }

    /**
     * Change JS handler for cancel/close - button from default "javascript:history.back();";<br/><br/>
     * Hide button, if set to empty string.<br/><br/>
     * <b>!! Attention !!</b>
     * don't use double quotes in function!
     * @param string $strOnCancel
     */
    public function setOnCancel(string $strOnCancel) : void 
    {
        $this->strOnCancel = $strOnCancel;
    }
    
    /**
     * Add any element to the form.
     * @param FormElement $oElement
     */
    public function addElement(FormElement $oElement) : void 
    {
        $this->iLastTabindex += $oElement->setTabindex($this->iLastTabindex + 1);
        if ($oElement->bCreateScript) {
            $this->aScriptElements[] = $oElement;
        }
        if ($oElement->bCreateStyle) {
            $this->aStyleElements[] = $oElement;
        }
    }
    
    /**
     * Create HTML for defined form.
     * @return string
     */
    public function getForm() : string
    {
        $strHTML = PHP_EOL;
        $strHTML .= '<!-- start autogenerated form -->' . PHP_EOL;
        
        if ($this->getDebugMode()) {
            // in debug environment we give alert if scriptfile is missing!
            $strHTML .= "<script>if (typeof FormDataValidator === 'undefined') {";
            $strHTML .= "displayJSError('You must include <b>&lt;FormDataValidator.js&gt;</b> for Form Validation!', 'Warning');" . PHP_EOL;
            $strHTML .= "}</script>" . PHP_EOL;
        }
        
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
        $strHTML .= '<div id=errorPopup onclick="closeErrorPopup(this);"></div>' . PHP_EOL;
        $iCnt = count($this->aChild);
        for ($i = 0; $i < $iCnt; $i++) {
            $strHTML .= $this->aChild[$i]->GetHTML();
        }
        
        $strHTML .= '</form>' . PHP_EOL;
        $strHTML .= '<!-- end autogenerated form -->' . PHP_EOL;
        $strHTML .= PHP_EOL;
        
        return $strHTML;
    }
    
    /**
     * Build needed JS script.
     * - for validation
     * - scripts of all childs that have own script
     * @return string
     */
    public function getScript() : string
    {
        $strScript = '';
        if ($this->getDebugMode()) {
            $strScript .= 
                "function displayJSError(msg, level)" . PHP_EOL .
                "{" . PHP_EOL .
                "    let div = document.createElement('div');" . PHP_EOL .
                "    div.id = 'JSError';" . PHP_EOL .
                "    let header = document.createElement('h1');" . PHP_EOL .
                "    div.appendChild(header);" . PHP_EOL .
                "    let body = document.createElement('p');" . PHP_EOL .
                "    div.appendChild(body);" . PHP_EOL .
                "    header.innerHTML = 'Javascript ' + level;" . PHP_EOL .
                "    body.innerHTML = msg;" . PHP_EOL .
                "    document.body.insertBefore(div, document.body.firstChild);" . PHP_EOL .
                "}" . PHP_EOL;
        }
        
        $strScript .= "function validateForm()" . PHP_EOL;
        $strScript .= "{" . PHP_EOL;
        $strScript .= "    var FDV = new FormDataValidator('" . $this->strID . "');" . PHP_EOL;
        $strScript .= "    return FDV.validate();" . PHP_EOL;
        $strScript .= "}" . PHP_EOL;
        
        foreach ($this->aScriptElements as $oElement) {
            $strScript .= $oElement->getScript();
        }
        return $strScript;
    }
    
    /**
     * Build needed CSS styles.
     * - body, if form width defined (TODO: better set width of form-element??)
     * - styles of all childs that have own styles
     * @return string
     */
    public function getStyle() : string 
    {
        $strStyle = '';
        if ($this->iWidth > 0) {
            $strStyle  = "body { width: " . $this->iWidth . "px;}" . PHP_EOL;
        }
        foreach ($this->aStyleElements as $oElement) {
            $strStyle .= $oElement->getStyle();
        }
        return $strStyle;   
    }
}
