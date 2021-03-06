<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Canvas element.
 * All attributes and styles except height and width (set with the constructor)
 * can be set through the methods <ul>
 * <li> addAttribute() </li>
 * <li> addStyle() </li></ul>
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormCanvas extends FormInput
{
    /**
     * Create o canvas element.
     * @param string $strID
     * @param int $iWidth
     * @param int $iHeight
     */
    public function __construct(string $strID, int $iWidth, int $iHeight)
    {
        $this->oFlags = new FormFlags();
        $this->strID = $strID;

        // Note: set attributes for width and height-styles will change internal behaviour of canvas
        $this->addAttribute('height', (string)$iHeight);
        $this->addAttribute('width', (string)$iWidth);
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     * @internall
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        // id comes from FormElement::readAdditionalXML() !!
        $iWidth = self::getAttribInt($oXMLElement, 'width', 100);
        $iHeight = self::getAttribInt($oXMLElement, 'height', 100);
        $oFormElement = new self('', $iWidth, $iHeight);
        $oFormParent->add($oFormElement);
        $oFormElement->readAdditionalXML($oXMLElement);
        return $oFormElement;
    }

    /**
     * Build the HTML-notation for the cancas element.
     * @return string
     * @internal
     */
    public function getHTML() : string
    {
        $strHTML = $this->buildContainerDiv();

        $strHTML .= '   <canvas id="' . $this->strID . '"';
        $strHTML .= $this->buildStyle();
        $strHTML .= $this->buildAttributes();
        $strHTML .= '></canvas>' . PHP_EOL;
        $strHTML .= '</div>' . PHP_EOL;

        return $strHTML;
    }
}
