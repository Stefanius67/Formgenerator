<?php

declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Element to select file(s) for upload supporting drag'n drop.
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormDropFile extends FormInput
{
    protected string $strSelectTxt = 'Datei auswählen...';
    protected string $strDropTxt = 'oder hierher ziehen.';
    protected bool $bMultiSelect = false;
    protected int $iRows = -1;
    protected int $iCols = -1;

    /**
     * Create a element to upload file(s) supporting drag'n drop.
     * @param string $strName
     * @param int $iCols
     * @param int $iRows
     * @param string $strWidth
     * @param int $wFlags
     */
    public function __construct(string $strName, int $iCols = 2, int $iRows = -1, string $strWidth = '95%', int $wFlags = 0)
    {
        $this->iCols = $iCols;
        $this->iRows = $iRows;
        parent::__construct($strName, $strWidth, $wFlags);

        $this->bMultiSelect = $this->oFlags->isSet(FormFlags::MULTI_SELECT);
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     * @internal
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        $strText = self::getAttribString($oXMLElement, 'text');
        $wFlags = self::getAttribFlags($oXMLElement);
        $strLabelFor = self::getAttribString($oXMLElement, 'for');
        $oFormElement = new self($strText, $wFlags, $strLabelFor);
        $oFormParent->add($oFormElement);
        $oFormElement->readAdditionalXML($oXMLElement);
        return $oFormElement;
    }

    /**
     * Build the HTML-notation for the static text
     * @return string
     * @internal
     */
    public function getHTML() : string
    {
        $this->processFlags();
        $this->setSize();
        $strHTML = $this->buildContainerDiv();

        $strStyle = '';
        if ($this->iRows > 0) {
            $strStyle = 'style="height: ' . (30 + $this->iRows * 26) . 'px"';
        }
        $strHTML .= '    <div class="dropfile" ' . $strStyle . '>' . PHP_EOL;
        $strHTML .= '        <label>' . PHP_EOL;
        $strHTML .= '            <span class="fileselect">Dateien auswählen</span>' . PHP_EOL;
        $strHTML .= '            <span class="droptext">oder hierher ziehen.</span>' . PHP_EOL;
        $strHTML .= '            <input type="file" name="files[]" multiple />' . PHP_EOL;
        $strHTML .= '        </label>' . PHP_EOL;
        $strHTML .= '        <div class="selectedfiles"></div>' . PHP_EOL;
        $strHTML .= '    </div>' . PHP_EOL;

        $strHTML .= '</div>' . PHP_EOL;

        return $strHTML;
    }
}
