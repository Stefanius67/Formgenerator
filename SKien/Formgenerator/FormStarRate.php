<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Element for rating input with stars.
 *
 * This element can be used to select rating between 1 ... n stars. It is based
 * on radio-input + CSS and uses UTF-8 Symbols for the stars - no Javascript
 * and images are needed!
 *
 * The titles of the stars are displayed as tooltip and can be set
 * - by calling the `setTitles()` method
 * - within the XML definition
 * - in the configuration
 *
 * (Method/XML overwrites config overwrites default)
 *
 * The count of titles also results in the number of stars.
 *
 * Default setting is 5 Stars (*'terrible', 'not good', 'average', 'good', 'verry good'*)
 *
 * By default the submitted value is an integer between 1 ... n (representing the count of stars)
 * wich can be changed to title of the selected star using `setSubmitTitle()` method.
 *
 * @SKienImage FormStarRate.png
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormStarRate extends FormInput
{
    /** default count of stars */
    protected const STAR_COUNT = 5;
    /** default titles for the stars */
    protected const STAR_TITELS = ['terrible', 'not good', 'average', 'good', 'verry good'];

    /** @var array titles for the stars (from 0 ... n-1) */
    protected ?array $aTitles = null;
    /** @var bool $bSubmitTitle submit the title of the selected star instead of index */
    protected bool $bSubmitTitle = false;

    /**
     * Create a star rating element.
     * @param string $strName   name AND id of the element
     * @param int $wFlags    (default: 0)
     */
    public function __construct(string $strName, int $wFlags = 0)
    {
        $this->oFlags = new FormFlags($wFlags);
        $this->strName = $strName;
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     * @internal
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        $strName = self::getAttribString($oXMLElement, 'name', '');
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
        $aTitles = $oXMLElement->getElementsByTagName('title');
        if ($aTitles->length > 0) {
            $this->aTitles = [];
            $i = 0;
            foreach ($aTitles as $oTitle) {
                $this->aTitles[$i++] = $oTitle->nodeValue;
            }
        }
        $this->bSubmitTitle = self::getAttribBool($oXMLElement, 'submittitle', false);
    }

    /**
     * We 'ask' the configuration for count and titles.
     * $this->oFG is not available until the parent is set!
     */
    protected function onParentSet() : void
    {
        if ($this->aTitles === null) {
            $this->aTitles = $this->oFG->getConfig()->getArray('StarRate.Titles', self::STAR_TITELS);
        }
    }

    /**
     * Build the HTML-markup for the radio group.
     * @return string
     * @internal l
     */
    public function getHTML() : string
    {
        $this->processFlags();

        $strStyle = '';
        if ($this->oFlags->isSet(FormFlags::ALIGN_CENTER)) {
            $strStyle = 'text-align: center;';
        } else if ($this->oFlags->isSet(FormFlags::ALIGN_RIGHT)) {
            $strStyle = 'text-align: right;';
        }

        $strHTML = $this->buildContainerDiv($strStyle) . PHP_EOL;
        $strHTML .= '    <div class="starrate">' . PHP_EOL;

        $iSelect = intval($this->oFG->getData()->getValue($this->strName));

        if (is_array($this->aTitles)) {
            $iStarCount = count($this->aTitles);
            for ($iStar = $iStarCount; $iStar > 0; $iStar--) {
                $strInputClassCSS = ($iStar == $iStarCount) ? ' class="best"' : '';
                $strId = $this->strName . 'Star' . $iStar;
                $strTitle = ($this->aTitles !== null && isset($this->aTitles[$iStar - 1])) ? $this->aTitles[$iStar - 1] : $iStar . ' Star';
                $strValue = ($this->bSubmitTitle) ? $strTitle : $iStar;

                $strHTML .= '        <input' . $strInputClassCSS;
                $strHTML .= ' type="radio" id="' . $strId . '"';
                $strHTML .= ($iSelect == $iStar) ? ' checked' : '';
                $strHTML .= ' name="' . $this->strName . '"';
                $strHTML .= $this->buildAttributes();
                $strHTML .= ' value="' . $strValue . '"/>' . PHP_EOL;

                $strHTML .= '        <label for="' . $strId . '"';
                $strHTML .= ' id="' . $strId . 'Label"';
                $strHTML .= ' title="' . $strTitle . '"></label>' . PHP_EOL;
            }
            $strHTML .= '    </div>' . PHP_EOL;

            $strHTML .= '</div>' . PHP_EOL;
        }

        return $strHTML;
    }

    /**
     * Set the titles for the stars.
     * The count of titles also results in the number of stars!
     * @param array $aTitles    [0]: lowest [n-1]: highest
     */
    public function setTitles(array $aTitles) : void
    {
        $this->aTitles = $aTitles;
    }

    /**
     * Submit the title of the selected star instead of index.
     * @param bool $bSet
     */
    public function setSubmitTitle(bool $bSet = true) : void
    {
        $this->bSubmitTitle = $bSet;
    }
}
