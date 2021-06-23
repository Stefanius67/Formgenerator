<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Text Area element.
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormTextArea extends FormInput
{
    /** @var int col count for the textarea     */
    protected int $iCols;
    /** @var int rowcount for the textarea     */
    protected int $iRows;

    /**
     * Create a textarea element.
     * @param string $strName   Name (if no ID specified, name is used also as ID)
     * @param int $iCols        col count
     * @param int $iRows        row count
     * @param string $strWidth  width of the textarea
     * @param int $wFlags       any combination of FormFlag constants
     */
    public function __construct(string $strName, int $iCols, int $iRows, string $strWidth = '95%', int $wFlags = 0)
    {
        parent::__construct($strName, -1, $wFlags);
        $this->iCols = $iCols;
        $this->iRows = $iRows;
        $this->addStyle('width', $strWidth);
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     * @internal
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        $strName = self::getAttribString($oXMLElement, 'name', '');
        $iCols = self::getAttribInt($oXMLElement, 'cols', 80);
        $iRows = self::getAttribInt($oXMLElement, 'rows', 10);
        $strWidth = self::getAttribString($oXMLElement, 'width', '95%');
        $wFlags = self::getAttribFlags($oXMLElement);
        $oFormElement = new self($strName, $iCols, $iRows, $strWidth, $wFlags);
        $oFormParent->add($oFormElement);
        $oFormElement->readAdditionalXML($oXMLElement);
        return $oFormElement;
    }

    /**
     * Build the HTML-notation for the textarea
     * @return string
     * @internal
     */
    public function getHTML() : string
    {
        $this->processFlags();

        $this->strID = $this->strID ?: $this->strName;

        $strValue = '';
        if (!$this->oFlags->isSet(FormFlags::SET_JSON_DATA)) {
            $strValue = $this->oFG->getData()->getValue($this->strName);

            // CR only relevant for Textareas ...
            if ($this->oFlags->isSet(FormFlags::REPLACE_BR_CR)) {
                $strValue = str_replace('<br>', "\n", $strValue);
                $strValue = str_replace('<br/>', "\n", $strValue);
                $strValue = str_replace('<br />', "\n", $strValue);
            }
        }

        $strHTML = $this->buildContainerDiv();
        $strHTML .= '<textarea';
        $strHTML .= ' name="' . $this->strName . '"';
        $strHTML .= $this->buildClass();
        $strHTML .= $this->buildID();
        $strHTML .= ' cols="' . $this->iCols . '"';
        $strHTML .= ' rows="' . $this->iRows . '"';
        $strHTML .= $this->buildStyle();
        $strHTML .= $this->buildAttributes();
        $strHTML .= $this->buildTabindex();
        $strHTML .= '>' . $strValue . '</textarea>';
        $strHTML .= $this->buildSelectButton('picker_top');

        $strHTML .= '</div>' . PHP_EOL;

        return $strHTML;
    }
}
