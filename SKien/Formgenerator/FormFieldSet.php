<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Class to create fieldset as parent of lines.
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormFieldSet extends FormCollection
{
    /** legend of the fieldset contains text    */
    const   TEXT    = 0;
    /** legend of the fieldset contains an image    */
    const   IMAGE   = 1;

    /** @var string text or image for the legend     */
    protected string $strLegend;
    /** @var int type of the legend (FormFieldSet::TXET or FormFieldSet::IMAGE)    */
    protected int $iType;
    /** @var int height of the legend image     */
    protected int $iImageHeight;

    /**
     * Creates a legend element.
     * If legend should display an image with height other than 12px, this height
     * must be set with setImageHeight().
     * @param string $strLegend text or image for the legend
     * @param string $strID     ID of the fieldset
     * @param int $iType        (FormFieldSet::TXET or FormFieldSet::IMAGE)
     */
    public function __construct(string $strLegend, string $strID = '', int $iType = self::TEXT)
    {
        parent::__construct(0);
        $this->strLegend = $strLegend;
        $this->strID = $strID;
        $this->iType = $iType;
        $this->iImageHeight = -1;
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     * @internal
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        $strLegend = self::getAttribString($oXMLElement, 'legend');
        $iType = self::TEXT;
        $strType = self::getAttribString($oXMLElement, 'type', 'TEXT');
        $strConstName = 'self::' . strtoupper($strType);
        if (defined($strConstName)) {
            $iType = constant($strConstName);
        } else {
            trigger_error('Unknown Constant [' . $strConstName . '] for the FieldSet-Type property!', E_USER_ERROR);
        }
        $oFormElement = new self($strLegend, '', $iType);
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
        if (self::hasAttrib($oXMLElement, 'imageheight')) {
            $this->setImageHeight(self::getAttribInt($oXMLElement, 'imageheight'));
        }
    }

    /**
     * Set height of legend image in pixels.
     * The default-height is 12px.
     * @param int $iHeight
     */
    public function setImageHeight(int $iHeight) : void
    {
        $this->iImageHeight = $iHeight;
    }

    /**
     * Build the HTML-notation for the fieldset.
     * @return string
     * @internall
     */
    public function getHTML() : string
    {
        $strHTML  = '';
        $strHTML .= '<fieldset';
        $strHTML .= $this->buildID();
        $strHTML .= $this->buildStyle();
        $strHTML .= $this->buildAttributes();
        $strHTML .= ">" . PHP_EOL;
        if (!empty($this->strLegend)) {
            $strHTML .= '   <legend>';
            if ($this->iType == self::TEXT) {
                $strHTML .= $this->strLegend;
            } else {
                // special case for style because legend is not treated as standalone element...
                $this->iImageHeight > 0 ? $strStyle = ' style="height: ' . $this->iImageHeight . 'px;"' : $strStyle = '';
                $strHTML .= '<img src="' . $this->strLegend . '" alt="Legend"' . $strStyle . '>';
            }
            $strHTML .= '</legend>' . PHP_EOL;
        }
        $iCnt = count($this->aChild);
        for ($i = 0; $i < $iCnt; $i++) {
            $strHTML .= $this->aChild[$i]->GetHTML();
        }
        $strHTML .= '</fieldset>' . PHP_EOL;
        return $strHTML;
    }
}
