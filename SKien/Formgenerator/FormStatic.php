<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Static Text element.
 *
 * The FormStatic element can be used for different purposes:
 * - just for the normal output of a text
 * - as a label that is assigned to a specific input element
 *   - set the `$strLabelFor` param in the constructor or use the `setLabelFor()` method
 * - As a hint
 *   - set the `FormFlags::HINT` flag
 * - To display an error message
 *   - set the `FormFlags::ERROR` flag
 * - As an information field
 *   - set the `FormFlags::INFO` flag
 *
 * @SKienImage FormStatic.png
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormStatic extends FormElement
{
    /** @var string the text to display     */
    protected string $strText;
    /** @var string the if set, label with for attribute is created     */
    protected string $strLabelFor = '';

    /**
     * Create a static text element.
     * @param string $strText       text to display
     * @param int $wFlags           any combination of FormFlag constants
     * @param string $strLabelFor   crate label for this element
     */
    public function __construct(string $strText, int $wFlags = 0, string $strLabelFor = '')
    {
        $this->strText = $strText;
        $this->strLabelFor = $strLabelFor;
        parent::__construct($wFlags);
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     * @internal
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        $strText = self::getAttribString($oXMLElement, 'text', '');
        $wFlags = self::getAttribFlags($oXMLElement);
        $strLabelFor = self::getAttribString($oXMLElement, 'for', '');
        $oFormElement = new self($strText, $wFlags, $strLabelFor);
        $oFormParent->add($oFormElement);
        $oFormElement->readAdditionalXML($oXMLElement);

        return $oFormElement;
    }

    /**
     * Set the control for which a label should be created.
     * @param string $strLabelFor
     */
    public function setLabelFor(string $strLabelFor) : void
    {
        $this->strLabelFor = $strLabelFor;
    }

    /**
     * Build the HTML-notation for the static text
     * @return string
     * @internal
     */
    public function getHTML() : string
    {
        // no container div!
        $this->addStyle('float', 'left');
        $strWidth = ($this->oParent ? $this->oParent->getColWidth($this->iCol) : '');
        if (!empty($strWidth)) {
            $this->addStyle('width', $strWidth);
        }
        if ($this->oFlags->isSet(FormFlags::ALIGN_RIGHT)) {
            $this->addStyle('text-align', 'right');
        } else if ($this->oFlags->isSet(FormFlags::ALIGN_CENTER)) {
            $this->addStyle('text-align', 'center');
        }
        if ($this->oFlags->isSet(FormFlags::BOLD)) {
            $this->addStyle('font-weight', 'bold');
        }

        $strHTML  = '';
        $strHTML .= '<div';
        $strHTML .= $this->buildID();
        $strHTML .= $this->buildClass();
        $strHTML .= $this->buildStyle();
        $strHTML .= $this->buildAttributes();
        $strHTML .= '>';
        if (strlen($this->strLabelFor) > 0) {
            $strHTML .= '<label for="' . $this->strLabelFor . '">' . $this->strText . '</label>';
        } else {
            $strHTML .= $this->strText;
        }
        $strHTML .= '</div>' . PHP_EOL;

        return $strHTML;
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::buildClass()
     */
    protected function buildClass() : string
    {
        $strSep = '';
        if (strlen($this->strClass) !== 0) {
            $strSep = ' ';
        }
        if ($this->oFlags->isSet(FormFlags::ERROR)) {
            $this->strClass .= $strSep . 'error';
        } else if ($this->oFlags->isSet(FormFlags::HINT)) {
            $this->strClass = $strSep . 'hint';
        } else if ($this->oFlags->isSet(FormFlags::INFO)) {
            $this->strClass = $strSep . 'forminfo';
        }
        return parent::buildClass();
    }
}
