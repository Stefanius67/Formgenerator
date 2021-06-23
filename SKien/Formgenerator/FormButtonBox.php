<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Button-Box with standard buttons to control the form.
 *
 * Supports the most used 'control' buttons for a form like
 * `[OK]` `[Save]` `[Cancel]` ... <br/>
 * Custom defined buttons can also be added.<br/>
 * Language can be configured through the config file.
 * <br/>
 * > Note: <br/>
 *   Alignment `FormFlags::ALIGN_CENTER` / `FormFlags::ALIGN_RIGHT` dont affect the
 *   alignment of the text within the buttons but the alignment of the buttons within the line!
 *
 *
 * All buttons marked as 'submit' causes the submit action for the form. All other buttons
 * call the JS function `[Btn-Id]Clicked()`  that must be implemented.
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormButtonBox extends FormElement
{
    /**  insert custom button at first position   */
    public const FIRST      = 0;
    /**  insert custom button at last position   */
    public const LAST       = -1;
    /** An `[OK]` button for submit.     */
    public const OK         = 0x0001;
    /** An `[Open]` button for submit.     */
    public const OPEN       = 0x0002;
    /** A `[Save]` button for submit.     */
    public const SAVE       = 0x0004;
    /** A `[Yes]` button for submit.     */
    public const YES        = 0x0008;
    /** A `[No]` button.     */
    public const NO         = 0x0010;
    /** A `[Cancel]` button.     */
    public const CANCEL     = 0x0020;
    /** A `[Close]` button.     */
    public const CLOSE      = 0x0040;
    /** A `[Discard]` button.     */
    public const DISCARD    = 0x0080;
    /** An `[Apply]` button for submit.     */
    public const APPLY      = 0x0100;
    /** A `[Reset]` button.     */
    public const RESET      = 0x0200;
    /** A `[Retry]` button for submit.     */
    public const RETRY      = 0x0400;
    /** An `[Ignore]` button. */
    public const IGNORE     = 0x0800;
    /** A `[Back]` button.     */
    public const BACK       = 0x1000;
    /** `[Yes]` `[No]` `[Cancel]` buttons.     */
    public const YES_NO_CANCEL = self::YES | self::NO | self::CANCEL;
    /** `[Save]` `[Cancel]` buttons.     */
    public const SAVE_CANCEL = self::SAVE | self::CANCEL;

    /** @var integer Buttons, the box containing     */
    protected int $iBtns = 0;
    /** @var array user defined button(s)     */
    protected array $aCustomButtons = [];

    /**
     * Create a FormButtonBox.
     * @param int $iBtns    any combination of the FormButtonBox::XXX constants
     */
    public function __construct(int $iBtns, int $wFlags = 0)
    {
        $this->iBtns = $iBtns;
        parent::__construct($wFlags);
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     * @internal
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        $iBtns = 0;
        if (($aBtns = self::getAttribStringArray($oXMLElement, 'buttons')) !== null) {
            foreach ($aBtns as $strBtn) {
                $strConstName = 'self::' . strtoupper($strBtn);
                if (defined($strConstName)) {
                    $iBtns += constant($strConstName);
                } else {
                    trigger_error('Unknown Constant [' . $strConstName . '] for the Button property!', E_USER_ERROR);
                }
            }
        }
        $wFlags = self::getAttribFlags($oXMLElement);
        $oFormElement = new self($iBtns, $wFlags);
        $oFormParent->add($oFormElement);
        $oFormElement->readAdditionalXML($oXMLElement); // TODO: support of custom buttons through XML

        return $oFormElement;
    }

    /**
     * Add custom button to the buttonbox.
     * Position of the button inside the box can be specified with the param $iAfterBtn: <ul>
     * <li> self::FIRST </li>
     * <li> self::LAST </li>
     * <li> other valid Button const: the custom Button appears after this button </li></ul>
     * @param string $strText   Test to display
     * @param string $strID     ID of the button
     * @param int $iAfterBtn    Position where to insert in the range of buttons
     * @param bool $bSubmit     if true, the button submits the form.
     */
    public function addButton(string $strText, string $strID, int $iAfterBtn = self::LAST, bool $bSubmit = false) : void
    {
        $this->aCustomButtons[$iAfterBtn] = ['text' => $strText, 'id' => $strID, 'type' => ($bSubmit ? 'submit' : 'button')];
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::getHTML()
     * @internal
     */
    public function getHTML() : string
    {
        if ($this->iBtns === 0) {
            return '';
        }
        if ($this->oFlags->isSet(FormFlags::ALIGN_CENTER)) {
            $this->addStyle('text-align', 'center');
        } else if ($this->oFlags->isSet(FormFlags::ALIGN_RIGHT)) {
            $this->addStyle('text-align', 'right');
        }

        $aButtonDef = $this->loadButtonDef();

        $strHTML = '<div id=buttonbox' . $this->buildStyle() . '>' . PHP_EOL;
        $iBtn = 0x0001;
        $strHTML .= $this->getCustomButton(self::FIRST);
        while ($iBtn < 0xffff) {
            if (($this->iBtns & $iBtn) != 0) {
                $strHTML .= $this->getButton($aButtonDef[$iBtn]);
            }
            $strHTML .= $this->getCustomButton($iBtn);
            $iBtn = $iBtn << 1;
        }
        $strHTML .= $this->getCustomButton(self::LAST);
        $strHTML .= '</div>' . PHP_EOL;

        return $strHTML;
    }

    /**
     * Set the tab index of first button.
     * Method is called from the PageGenerator after an element is added to the form.
     * @param int $iTabindex
     * @return int the count of buttons (-> number tabindexes 'needed')
     * @internal
     */
    public function setTabindex(int $iTabindex) : int
    {
        $this->iTabindex = $iTabindex;
        return $this->getButtonCount();
    }

    /**
     * Build the markup for the button.
     * @param array $aBtn
     * @return string
     */
    protected function getButton(array $aBtn) : string
    {
        $strHTML = '  <input id="' . $aBtn['id'] . '"';
        $strHTML .= ' type="' . $aBtn['type'] . '"';
        $strHTML .= ' tabindex="' . $this->iTabindex++ . '"';
        if ($this->oFlags->isSet(FormFlags::READ_ONLY | FormFlags::DISABLED)) {
            if ($aBtn['type'] == 'submit') {
                $strHTML .= ' disabled';
            }
        } else if ($aBtn['type'] != 'submit') {
            $strHTML .= ' onclick="' . $aBtn['id'] . 'Clicked();"';
        }
        $strHTML .= ' value="' . $aBtn['text'] . '"';
        $strHTML .= '>' . PHP_EOL;

        return $strHTML;
    }

    /**
     * Build custom button, if defined for the requested position.
     * @param int $iAfterBtn
     * @return string
     */
    protected function getCustomButton(int $iAfterBtn) : string
    {
        if (!isset($this->aCustomButtons[$iAfterBtn])) {
            return '';
        }
        return $this->getButton($this->aCustomButtons[$iAfterBtn]);
    }

    /**
     * Get the number of buttons the box contains.
     * @return int
     */
    protected function getButtonCount() : int
    {
        $iCount = 0;
        $iBtns = $this->iBtns;
        while ($iBtns) {
            $iCount += ($iBtns & 1);
            $iBtns >>= 1;
        }
        return $iCount + count($this->aCustomButtons);
    }

    /**
     * Get Textlabels for all buttons.
     * Default they are initialized with the english Text.
     * Configuration can contain localization.
     * @return array
     */
    protected function loadButtonDef() : array
    {
        $aButtonDef = [
            self::OK => ['text' => 'OK', 'id' => 'btnOK', 'type' => 'submit'],
            self::OPEN => ['text' => 'Open', 'id' => 'btnOpen', 'type' => 'button'],
            self::SAVE => ['text' => 'Save', 'id' => 'btnSave', 'type' => 'submit'],
            self::YES => ['text' => 'Yes', 'id' => 'btnYes', 'type' => 'submit'],
            self::NO => ['text' => 'No', 'id' => 'btnNo', 'type' => 'button'],
            self::CANCEL => ['text' => 'Cancel', 'id' => 'btnCancel', 'type' => 'button'],
            self::CLOSE => ['text' => 'Close', 'id' => 'btnClose', 'type' => 'button'],
            self::DISCARD => ['text' => 'Discard', 'id' => 'btnDiscard', 'type' => 'button'],
            self::APPLY => ['text' => 'Apply', 'id' => 'btnApply', 'type' => 'submit'],
            self::RESET => ['text' => 'Reset', 'id' => 'btnReset', 'type' => 'button'],
            self::RETRY => ['text' => 'Retry', 'id' => 'btnRetry', 'type' => 'submit'],
            self::IGNORE => ['text' => 'Ignore', 'id' => 'btnIgnore', 'type' => 'button'],
            self::BACK => ['text' => 'Back', 'id' => 'btnBack', 'type' => 'button'],
        ];

        $aConfig = $this->oFG->getConfig()->getArray('ButtonBox.ButtonText');
        // To make it easier to read, the configuration contains the names of the constants
        // as keys. So we have to convert the names into the values and assign the texts
        // accordingly.
        foreach ($aConfig as $strName => $strText) {
            $iBtn = constant('self::' . $strName);
            $aButtonDef[$iBtn]['text'] = $strText;
        }
        return $aButtonDef;
    }
}

