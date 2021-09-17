<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Checkbox input field.
 * As default, the value 'on' is submitted for a checked element. This value can be
 * changed with the `setBtnValue()` method.<br/>
 *
 *
 * The checkbox element is a special case, as it behaves particularly regarding to the
 * transferred value and does not support read-only mode.
 *
 *
 * > <b>transfering 'unchecked' state</b><br/>
 *   A hidden edit field with same name is inserted BEFORE the checkbox is crated and
 *   always set its value to 'off'.<br/>
 *   If the checkbox is activated, 'on' (or the value set with `setBtnValue`) is posted
 *   as normal.<br/>
 *   However, since the value of a non-activated checkbox is not transferred at all, in this
 *   case the hidden field defined in the previous order comes into play and the fixed value
 *   'off' is transferred.<br/>
 *   This is particularly helpful/important if a database operation is to be carried out
 *   dynamically on the basis of the transferred fields: If the 'off' were not transferred,
 *   the field would not be taken into account either and would therefore never be set from
 *   'on' to 'off'!!
 *
 *
 * > <b>workaround for readonly state</b><br/>
 *   Because checkboxes don't support readonly-attribute and disabled checkboxes
 *   are not posted to reciever, we insert an hidden field with name and id to keep
 *   value 'alive'.<br/>
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormCheck extends FormInput
{
    /** @var string value for the button, if checked (default = 'on')  */
    protected string $strBtnValue = 'on';

    /**
     * Create a checkbox.
     * @param string $strName   name AND id of the element
     * @param int $wFlags       any combination of FormFlag constants
     * @param string $strSuffix Text after the checkbox (default: '')
     */
    public function __construct(string $strName, int $wFlags = 0, string $strSuffix = '')
    {
        $this->oFlags = new FormFlags($wFlags);
        $this->strName = $strName;
        $this->strSuffix = $strSuffix;
        if ($this->oFlags->isSet(FormFlags::READ_ONLY | FormFlags::DISABLED)) {
            $this->addAttribute('disabled');
        }
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     * @internal
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        $strName = self::getAttribString($oXMLElement, 'name');
        $wFlags = self::getAttribFlags($oXMLElement);
        $oFormElement = new self($strName, $wFlags);
        $oFormParent->add($oFormElement);
        $oFormElement->readAdditionalXML($oXMLElement);
        return $oFormElement;
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::readAdditionalXML()
     * @internal
     */
    public function readAdditionalXML(\DOMElement $oXMLElement) : void
    {
        parent::readAdditionalXML($oXMLElement);
        if (self::hasAttrib($oXMLElement, 'btnvalue')) {
            $strBtnValue = self::getAttribString($oXMLElement, 'btnvalue');
            $this->setBtnValue($strBtnValue);
        }
    }

    /**
     * Set the value that is posted, when the element is checked.
     * @param string $strBtnValue
     */
    public function setBtnValue(string $strBtnValue) : void
    {
        $this->strBtnValue = $strBtnValue;
    }

    /**
     * Build the HTML-markup for the checkbox.
     * @return string
     * @internal
     */
    public function getHTML() : string
    {
        $this->processFlags();
        $bChecked = $this->getBoolValue();
        if ($bChecked) {
            $this->addAttribute('checked');
        }

        $strHTML = $this->buildContainerDiv();

        $strHTML .= $this->buildUncheckedSurrogate();
        $strHTML .= $this->buildCheckBox();
        $strHTML .= $this->buildReadonlySurrogate($bChecked);

        if (!empty($this->strSuffix)) {
            $strHTML .= '&nbsp;' . $this->strSuffix;
        }

        $strHTML .= '</div>' . PHP_EOL;

        return $strHTML;
    }

    /**
     * Get the value as bool. <ul>
     * <li> 'real' bool values as is </li>
     * <li> numeric: 0 -> false, all other -> true </li>
     * <li> string: '1', 'on', 'true', 'yes' (case insensitive) -> true, all other -> false </li></ul>
     * @return bool
     */
    protected function getBoolValue() : bool
    {
        $bChecked = false;
        $value = $this->oFG->getData()->getValue($this->strName);
        if (is_bool($value)) {
            $bChecked = $value;
        } else {
            if (is_numeric($value)) {
                $bChecked = ($value !== 0);
            } else {
                $value = strtolower($value);
                $bChecked = in_array($value, ['1', 'on', 'true', 'yes']);
            }
        }
        return $bChecked;
    }

    /**
     * Build the checkbox.
     * @return string
     */
    protected function buildCheckBox() : string
    {
        $strHTML = '<input type="checkbox"';
        $strHTML .= $this->buildStyle();
        $strHTML .= $this->buildAttributes();
        $strHTML .= $this->buildTabindex();
        $strHTML .= ' id="' . $this->strName . '"';
        if (!$this->oFlags->isSet(FormFlags::READ_ONLY | FormFlags::DISABLED)) {
            $strHTML .= ' name="' . $this->strName . '"';
            $strHTML .= ' value="' . $this->strBtnValue . '"';
        }
        $strHTML .= '>';

        return $strHTML;
    }

    /**
     * Build the surrogate for unchecked box.
     * @return string
     */
    protected function buildUncheckedSurrogate() : string
    {
        // We insert a hidden edit field with same name BEFORE we create the checkbox
        // and  alwas set its value to 'off'.
        // If the checkbox is activated, 'on' (or the value set with BtnValue) is posted as normal.
        // However, since the value of a non-activated checkbox is not transferred at all, in this
        // case the hidden field defined in the previous order comes into play and the fixed value
        // 'off' is transferred.
        // This is particularly helpful/important if a database operation is to be carried out
        // dynamically on the basis of the transferred fields: If the 'off' were not transferred,
        // the field would not be taken into account either and would therefore never be set from
        // 'on' to 'off'!!
        $strHTML = '<input type="hidden" value="off" name="' . $this->strName . '">';

        return $strHTML;
    }

    /**
     * Build the surrogate in case of readonly checkbox.
     * @param bool $bChecked
     * @return string
     */
    protected function buildReadonlySurrogate(bool $bChecked) : string
    {
        // NOTE: because checkboxes don't support readonly-attribute and disabled checkboxes
        // are not posted to reciever, we insert an hidden field with name and id to keep
        // value 'alive'
        // -> so we dont set name and id for readonly or disabled checkbox!
        $strHTML = '';
        if ($this->oFlags->isSet(FormFlags::READ_ONLY | FormFlags::DISABLED)) {
            $strHTML .= '<input';
            $strHTML .= ' type="hidden"';
            $strHTML .= ' name="' . $this->strName . '"';
            $strValue = ($bChecked) ? $this->strBtnValue : 'off';
            $strHTML .= ' value="' . $strValue . '">';
        }
        return $strHTML;
    }
}
