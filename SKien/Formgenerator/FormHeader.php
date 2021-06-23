<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Class to display a header line inside of the form.
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormHeader extends FormElement
{
    /** @var string text for the header     */
    protected string $strText;
    /** @var int level of the HTML header element     */
    protected int $iLevel;

    /**
     * Create header element.
     * @param string $strText   Text to display
     * @param number $iLevel    level of the header (&lt;h1&gt; ... &lt;h5&gt;)
     */
    public function __construct(string $strText, $iLevel = 2)
    {
        parent::__construct(0);
        $this->strText = $strText;
        $this->iLevel = $iLevel;
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     * @internal
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        $strText = self::getAttribString($oXMLElement, 'text', '');
        $iLevel = self::getAttribInt($oXMLElement, 'level', 2);
        $oFormElement = new self($strText, $iLevel);
        $oFormParent->add($oFormElement);
        $oFormElement->readAdditionalXML($oXMLElement);

        return $oFormElement;
    }

    /**
     * Build the HTML-notation for the header text
     * @return string
     * @internal
     */
    public function getHTML() : string
    {
        $strHTML = '<h' . $this->iLevel . '>' . $this->strText . '</h' . $this->iLevel . '>' . PHP_EOL;
        return $strHTML;
    }
}
