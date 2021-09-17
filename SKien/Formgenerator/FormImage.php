<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Element to display image inside of a form.
 *
 * The image can be bound to an input field that contains the filename (-&gt; `src`)
 * of the image. This field can be readonly or hidden.
 * If an image is bound to an input field, there is also the posibilty to set a default
 * image, that is displayed, when the bounded field is empty.
 *
 * > <b>Example:</b><br/>
 *   The image is bounded to the field 'strPortrait' that contains the path of a
 *   portrait comming from a databaase record. For an empty/new record or even if
 *   no portrait has been selected so far, an specified default image will be displayed
 *   (e.g. an placeholder)
 *
 *
 *  An example for a bounded image can be found in ColumnForm.php and ColumnFormXML.php in
 *  the example directory.
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormImage extends FormElement
{
    /** standard delete image */
    const IMG_DELETE            = 1;
    /** standard search image */
    const IMG_SEARCH            = 2;
    /** standard search image */
    const IMG_BROWSE            = 3;
    /** standard image for date picker */
    const IMG_DATE_PICKER       = 4;
    /** standard image for time picker */
    const IMG_TIME_PICKER       = 5;
    /** standard image for dtu insert (DTU: Date-Time-User) */
    const IMG_DTU               = 6;

    /** @var string|int image to display     */
    protected $img;
    /** @var string CSS styles     */
    protected string $strStyle;
    /** @var string image is bound to this input element    */
    protected string $strBoundTo = '';
    /** @var string image to use, if no image set    */
    protected string $strDefaultImg = '';

    /**
     * Create image element.
     * @param string $strName       Name of the image
     * @param string|int $img       image to display or index to a standard image
     * @param string $strOnClick    JS onclick() handler
     * @param int $wFlags           any combination of FormFlag constants
     * @param string $strStyle      CSS style (default '')
     */
    public function __construct(string $strName, $img, string $strOnClick, int $wFlags = 0, string $strStyle = '')
    {
        parent::__construct($wFlags);
        $this->strName = $strName;
        $this->img = $img;
        $this->addAttribute('onclick', $strOnClick);

        if (strlen($strStyle) > 0) {
            $this->parseStyle($strStyle);
        }
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     * @internal
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        $strName = self::getAttribString($oXMLElement, 'name');
        $image = self::getAttribString($oXMLElement, 'image');
        $strConstName = 'self::' . strtoupper($image);
        if (defined($strConstName)) {
            $image = constant($strConstName);
        }
        $wFlags = self::getAttribFlags($oXMLElement);
        $oFormElement = new self($strName, $image, '', $wFlags);
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
        if (self::hasAttrib($oXMLElement, 'bindto')) {
            $this->bindTo(self::getAttribString($oXMLElement, 'bindto'));
        }
        if (self::hasAttrib($oXMLElement, 'default')) {
            $this->setDefault(self::getAttribString($oXMLElement, 'default'));
        }
    }

    /**
     * Bind the image to an input field that contains the imagepath.
     * @param string $strBoundTo    Name of the input element
     */
    public function bindTo(string $strBoundTo) : void
    {
        $this->strBoundTo = $strBoundTo;
    }

    /**
     * Set a default image, if no image set or the bounded input contains no data.
     * @param string $strDefault
     */
    public function setDefault(string $strDefault) : void
    {
        $this->strDefaultImg = $strDefault;
    }

    /**
     * Build the HTML-notation for the image.
     * @return string
     * @internal l
     */
    public function getHTML() : string
    {
        if (!isset($this->aStyle['vertical-align'])) {
            $this->addStyle('vertical-align', 'text-bottom');
        }
        $strStyle = '';
        if ($this->oFlags->isSet(FormFlags::ALIGN_CENTER)) {
            $strStyle = 'text-align: center;';
        } else if ($this->oFlags->isSet(FormFlags::ALIGN_RIGHT)) {
            $strStyle = 'text-align: right;';
        }
        $strHTML = $this->buildContainerDiv($strStyle);

        $strImg = $this->getImg();
        $strAlt = 'Image';
        $strHTML .= '<img src="' . $strImg . '" alt="' . $strAlt . '"';
        if (!empty($this->strName)) {
            $strHTML .= ' id="' . $this->strName . '"';
        }
        $strHTML .= $this->buildStyle();
        $strHTML .= $this->buildClass();
        $strHTML .= $this->buildID();
        $strHTML .= $this->buildAttributes();
        $strHTML .= '></div>' . PHP_EOL;

        return $strHTML;
    }

    /**
     * Get  the image to display.
     * Can be <ul>
     * <li> the image contained in the value of a bounded input field </li>
     * <li> a standard image specified by number </li>
     * <li> the image specified by the img property </li></ul>
     * @return string
     */
    protected function getImg() : string
    {
        $strImg = '';
        if (strlen($this->strDefaultImg) > 0) {
            $this->addAttribute('data-default', $this->strDefaultImg);
        }

        if (strlen($this->strBoundTo) > 0) {
            $this->addAttribute('data-bound-to', $this->strBoundTo);
            $strImg = $this->oFG->getData()->getValue($this->strBoundTo);
        } else if (is_numeric($this->img)) {
            [$strImg, $strTitle] = $this->oFG->getStdImage(intval($this->img));
            if (strlen($strTitle) > 0) {
                $this->addAttribute('title', $strTitle);
            }
        } else {
            $strImg = $this->img;
        }

        if (strlen($strImg) == 0) {
            $strImg = $this->strDefaultImg;
        }
        return $strImg;
    }
}
