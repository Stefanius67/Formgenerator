<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * WYSIWYG - HTML input using TinyMCE.
 *
 * @see https://www.tiny.cloud/docs/tinymce/6/
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormTinyMCE extends FormTextArea
{
    /**
     * Creates a TinyMCE WYSIWYG editor.
     * @param string $strName
     * @param int $iRows
     * @param string $strWidth
     * @param int $wFlags
     */
    public function __construct(string $strName, int $iRows, string $strWidth = '100%', int $wFlags = 0)
    {
        // add 2 rows to increase height for toolbar
        parent::__construct($strName, 0, $iRows + 2, $strWidth, $wFlags);
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
    }

    /**
     * Load some configuratin after parent set.
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::onParentSet()
     */
    protected function onParentSet() : void
    {
    }

    /**
     * Build TinyMCE specific styles.
     * @return string
     */
    public function getStyle() : string
    {
        $strStyle = '';
        return $strStyle;
    }
}
