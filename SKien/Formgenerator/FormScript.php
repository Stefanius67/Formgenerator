<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Class to insert a script tag inside of the form.
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormScript extends FormElement
{
    /** @var string text for the line label     */
    protected string $strScript;

    /**
     * Cretae a script tag inside of the form.
     * @param string $strScript     Script to insert
     */
    public function __construct(string $strScript)
    {
        parent::__construct(0);
        $this->strScript = $strScript;
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     * @internal
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        $strScript = $oXMLElement->textContent;
        $oFormElement = new self($strScript);
        $oFormParent->add($oFormElement);
        return $oFormElement;
    }

    /**
     * Insert the script at current position of the form.
     * @return string
     * @internal l
     */
    public function getHTML() : string
    {
        $strHTML = '<script>' . $this->strScript . '</script>' . PHP_EOL;
        return $strHTML;
    }
}
