<?php
namespace SKien\Formgenerator;

use SKien\Formgenerator\FormElement as FE;

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
class FormGenerator extends FormElement
{
    use GetFromSQL;
    
    /** save/submit button */
    const BTN_SAVE      = 'save';
    /** cancel/discard button */
    const BTN_CANCEL    = 'cancel';
    /** close button */
    const BTN_CLOSE     = 'close';
    
    /** JS code to close dialog */
    const CMD_CLOSE_DLG = "parent.document.getElementById('dialog').innerHTML = '';";
    
    /** @var int last tab position in the form   */
    protected int $iLastTab = 0;
    /** @var string timestamp of last modification  */
    protected ?string $tsModified = null;
    /** @var string user of last modification  */
    protected ?string $strUserModified = null;
    /** @var bool set the whole form to readonly  */
    protected bool $bReadOnly = false;
    /** @var string JS handler for onsubmit  */
    protected string $strOnSubmit = '';
    /** @var string JS handler for oncancel  */
    protected string $strOnCancel = '';
    /** @var int explicit width of the form in pixel  */
    protected int $iWidth = -1;
    /** @var int global flags for all form elements  */
    protected int $wGlobalFlags = 0;
    /** @var array elements that creates dynamic script */
    protected array $aScriptElements = [];
    /** @var array elements that creates dynamic CSS styles */
    protected array $aStyleElements = [];
    /** @var string path to the images  */
    protected string $strImgPath;
    /** @var string receiver of the form-action  */
    protected string $strActionReceiver;
    /** @var string URL params for the form action  */
    protected string $strAction;
    /** @var string form target  */
    protected string $strFormTarget = '';
    /** @var FormElement hidden element containing the referer of this form */
    protected ?FormElement $oReferer = null;
    /** @var bool hide the submit/cancel/close buttons  */
    protected bool $bHideButtons = false;
    /** @var array text for the submit/cancel/close buttons  */
    protected array $aBtnText;
    /** @var array array to hold elements for validation  */
    protected array $aValidate;
    
    /**
     * Create a FormGenerator.
     * Set some values to default.
     */
    public function __construct() 
    {
        $this->oFG = $this;
        $this->strID = 'auto_form';
        $this->aValidate = array('aMand' => array(), 'aEdit' => array(), 'aDate' => array(), 'aInt' => array(), 'aCur' => array(), 'aTime' => array());
        $this->aBtnText = array('save' => 'Speichern', 'cancel' => 'Abbrechen', 'close' => 'Schließen');
        $this->strOnSubmit = "ValidateForm();";
        $this->strOnCancel = "javascript:history.back();";
        $this->strImgPath = '../images/';
        $this->strAction = $_SERVER['QUERY_STRING'];
        $this->strActionReceiver = $_SERVER['PHP_SELF'] . '?';
        $strReferer = $_SERVER['HTTP_REFERER'] ?? '';
        $this->oReferer = $this->add(new FormInput('urlReferer', $strReferer, -1, self::HIDDEN));
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
     * Set the receiver of the formaction.
     * If not changed here, the current script ($_SERVER['PHP_SELF']) will
     * receive the form action.
     * @param string $strActionReceiver
     */
    public function setActionReceiver(string $strActionReceiver) : void 
    {
        // remove trailing '?' if exist
        $strActionReceiver = rtrim($strActionReceiver, '?');
        if (strpos($strActionReceiver, '?') !== false) {
            // URL params already exist - continue with '&'
            $strActionReceiver .= '&';
        } else {
            $strActionReceiver .= '?';
        }
        $this->strActionReceiver = $strActionReceiver;
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
        return $this->wGlobalFlags;
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
        $bReadOnly ? $this->wGlobalFlags |= self::DISABLED : $this->wGlobalFlags &= ~self::DISABLED;
    }

    /**
     * Don't create formbuttons save/cancel/close
     * @param bool $bHideButtons
     */
    public function hideButtons($bHideButtons = true) : void 
    {
        $this->bHideButtons = $bHideButtons;
    }
    
    /**
     * Set timestamp and user of last modification
     * @param string $tsModified timestamp from database
     * @param string $strUserModified
     */
    public function setLastModified(string $tsModified, string $strUserModified) : void 
    {
        $this->tsModified = $tsModified;
        $this->strUserModified = $strUserModified;
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
     * Changes default text for selected button
     * defaults:   
     * - 'save'     => 'Speichern'
     * - 'cancel'   => 'Abbrechen'
     * - 'close'    => 'Schließen'
     * 
     * @param string $strBtn   FormGenerator::BTN_SAVE, FormGenerator::BTN_CANCEL or FormGenerator::BTN_CLOSE
     * @param string $strText
     */
    public function setBtnText(string $strBtn, string $strText) : void 
    {
        if (isset($this->aBtnText[$strBtn])) {
            $this->aBtnText[$strBtn] = $strText;
        } else {
            trigger_error('invalid Button specified!', E_USER_WARNING);
        }
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
        if ($oElement->hasTab()) {
            $oElement->setTab(++$this->iLastTab);
        }
        if (!empty($oElement->strValidate) && ($oElement->wFlags & FE::HIDDEN) == 0) {
            array_push($this->aValidate[$oElement->strValidate], $oElement->strName);
            if (($oElement->wFlags & FE::MANDATORY) != 0) {
                array_push($this->aValidate['aMand'], $oElement->strName);
            }
        }
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
        $strHTML .= '<form';
        if (!empty($this->strAction)) {
            $strHTML .= ' action="';
            $strHTML .= $this->strActionReceiver;
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
        $iCnt = count($this->aChild);
        for ($i = 0; $i < $iCnt; $i++) {
            $strHTML .= $this->aChild[$i]->GetHTML();
        }
        
        if (!$this->bHideButtons) {
            $strHTML .= '<div id=formbuttons>' . PHP_EOL;
            if (!$this->bReadOnly) {
                if (!empty($this->strOnSubmit)) {
                    $strHTML .= '   <input id="btnSubmit" type="submit" tabindex="100" value="' . $this->aBtnText['save'] . '">' . PHP_EOL;
                }
                if (!empty($this->strOnCancel)) {
                    $strHTML .= '   <input id="btnCancel" type="button" tabindex="101" value="' . $this->aBtnText['cancel'] . '" onclick="' . $this->strOnCancel . '">' . PHP_EOL;
                }
            } else {
                $strHTML .= '   <input id="btnClose" type="button" tabindex="101" value="' . $this->aBtnText['close'] . '" onclick="' . $this->strOnCancel . '">' . PHP_EOL;
            }
            
            $strHTML .= '</div>' . PHP_EOL;
        }
        $strHTML .= $this->showLastModified();
        
        $strHTML .= '</form>' . PHP_EOL;
        $strHTML .= '<!-- end autogenerated form -->' . PHP_EOL;
        $strHTML .= PHP_EOL;
        
        return $strHTML;
    }
    
    /**
     * Build infos for last modification (if $this->tsModified set)
     * @return string
     */
    protected function showLastModified() : string 
    {
        $strHTML = '';
        if (!empty($this->tsModified)) {
            $strHTML .= '<h4>letzte &Auml;nderung am <span class="lastmodified">';
            $strHTML .= $this->tsFromSQL('d.m.Y H:i', $this->tsModified);
            $strHTML .= '</span> von <span class="lastmodified">';
            $strHTML .= $this->strUserModified;
            $strHTML .= '</span></h4>' . PHP_EOL;
        }
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
        $aArrays = array('aMand', 'aDate', 'aTime', 'aInt', 'aCur');
        
        $strScript  = 'function ValidateForm() {' . PHP_EOL;
        $iArrays = count($aArrays);
        for ($j = 0; $j < $iArrays; $j++) {
            $sep = '';
            $strScript .= '    var ' . $aArrays[$j] . ' = new Array(';
            $iCnt = count($this->aValidate[$aArrays[$j]]);
            for ($i = 0; $i < $iCnt; $i++) {
                $strScript .= $sep .  '"' . $this->aValidate[$aArrays[$j]][$i] . '"';
                $sep = ', ';
            }
            $strScript .= ');' . PHP_EOL;
        }
        
        $strScript .= PHP_EOL;
        $strScript .= '    return ValidateInput2(aMand, aDate, aTime, aInt, aCur);' . PHP_EOL;
        $strScript .= '}' . PHP_EOL;
        
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
