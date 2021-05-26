<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Element to select Color.
 * Uses the JSColor - picker version 2.4.5  (https://jscolor.com/)
 *
 * <br/><br/>
 * <b>!!! For commercial use take care of the JSColor licence conditions !!!</b>
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 * @link https://jscolor.com/
 */
class FormColor extends FormInput
{
    /**
     * Creates input field for color values.
     * @param string $strName
     * @param int|string $size number set the size-attribute, a string is used for the width attribute
     * @param int $wFlags    default 0
     */
    public function __construct(string $strName, $size = 6, int $wFlags = 0)
    {
        parent::__construct($strName, $size, $wFlags);
        $this->addAttribute('data-jscolor', '{}');
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        $strName = self::getAttribString($oXMLElement, 'name', '');
        $strSize = self::getAttribString($oXMLElement, 'size', '6');
        $wFlags = self::getAttribFlags($oXMLElement);
        $oFormElement = new self($strName, $strSize, $wFlags);
        $oFormParent->add($oFormElement);
        $oFormElement->readAdditionalXML($oXMLElement);
        return $oFormElement;
    }

    /**
     * Pass some presets for the color picker to JS.
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::onParentSet()
     */
    protected function onParentSet() : void
    {
        // If some presets for the picker are defined in the config, just set it:
        // - position (default: bottom)
        // - border color (no default value - set only if defined in the config)
        // - background color (no default value - set only if defined in the config)
        // - palette (no default value - set only if defined in the config)
        $aPreset = [
            'position' => $this->oFG->getConfig()->getString('Color.position', 'bottom'),
            'borderColor' => $this->oFG->getConfig()->getString('Color.borderColor', '#000000'),
            'backgroundColor' => $this->oFG->getConfig()->getString('Color.backgroundColor', '#FFFFFF'),
            'borderRadius' => $this->oFG->getConfig()->getInt('Color.borderRadius', 0),
            'padding' => $this->oFG->getConfig()->getInt('Color.padding', 12),
        ];
        $aPalette = $this->oFG->getConfig()->getArray('Color.palette');
        if (count($aPalette) > 0) {
            $aPreset['palette'] = $aPalette;
        }
        $this->oFG->addConfigForJS('Color', $aPreset);
    }
}
