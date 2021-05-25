<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Base-class for all elements intended to get user input.
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormInput extends FormElement
{
    /** @var string value input type    */
    protected string $strType = '';
    /** @var int|string size as number or as string including dimension ('%', 'px', 'em') */
    protected $size = 0;
    /** @var string image displayed, if selectbutton is enabled     */
    protected string $strSelectImg = '';
    /** @var string tooltip for selectbutton     */
    protected string $strSelectImgTitle = '';
    /** @var string folder to expand when call the filemanager     */
    protected string $strExpandFolder = '';
    /** @var string suffix directly after the element     */
    protected string $strSuffix = '';

    /**
     * @param string $strName Name (also used as ID, if not set separate)
     * @param int|string $size number set the size-attribute, a string is used for the width attribute
     * @param int $wFlags
     */
    public function __construct(string $strName, $size, int $wFlags = 0, int $iMaxLength = 0)
    {
        parent::__construct($wFlags);
        $this->strName = $strName;
        $this->size = $size;
        $this->strType = 'text';
        $this->strSelectImg = '';
        $this->strSelectImgTitle = '';
        $this->strSuffix = '';

        $this->addFlags($wFlags);
        if ($iMaxLength > 0) {
            $this->addAttribute('maxlength', (string)$iMaxLength);
        }
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        /* only while development
        if ($oXMLElement->nodeName !== 'Input') {
            trigger_error('Try to create Form' . $oXMLElement->nodeName . ' - Element from XML without defined method!', E_USER_ERROR);
        }
        */
        $strName = self::getAttribString($oXMLElement, 'name', '');
        $strSize = self::getAttribString($oXMLElement, 'size', '');
        $wFlags = self::getAttribFlags($oXMLElement);
        $oFormElement = new self($strName, $strSize, $wFlags);
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
        if (($strSuffix = self::getAttribString($oXMLElement, 'suffix')) !== null) {
            $this->setSuffix($strSuffix);
        }
        if (($strSelectImg = self::getAttribString($oXMLElement, 'selectimg')) !== null) {
            $this->setSelectImg($strSelectImg, self::getAttribString($oXMLElement, 'selectimgtitle', ''));
        }
        if (($strExpandFolder = self::getAttribString($oXMLElement, 'expandfolder')) !== null) {
            $this->setExpandFolder($strExpandFolder);
        }
    }

    /**
     * Set the maxlength attribute of the element.
     * @param int $iMaxLength
     */
    public function setMaxLength(int $iMaxLength) : void
    {
        $this->addAttribute('maxlength', (string)$iMaxLength);
    }

    /**
     * Set placeholder to display on empty input element.
     * @param string $strPlaceholder
     */
    public function setPlaceholder(string $strPlaceholder) : void
    {
        if (strlen($strPlaceholder) > 0) {
            $this->addAttribute('placeholder', $strPlaceholder);
        }
    }

    /**
     * set image and title for select-button (leave strImg blank for default 'search')
     * @param string $strImg
     * @param string $strTitle (default = '')
     */
    public function setSelectImg(string $strImg, string $strTitle = '') : void
    {
        $this->strSelectImg = $strImg;
        $this->strSelectImgTitle = $strTitle;
    }

    /**
     * Set the folder to expand when call the filemanager.
     * @param string $strExpandFolder
     */
    public function setExpandFolder(string $strExpandFolder) : void
    {
        $this->strExpandFolder = $strExpandFolder;
    }

    /**
     * @param string $strSuffix
     */
    public function setSuffix(string $strSuffix) : void
    {
        $this->strSuffix = $strSuffix;
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::onParentSet()
     */
    protected function onParentSet() : void
    {
        if ($this->oFlags->isSet(FormFlags::BROWSE_SERVER)) {
            $this->oFG->addConfigForJS('RichFilemanager', $this->oFG->getConfig()->getArray('RichFilemanager'));
        }
    }

    /**
     * Build the HTML-notation for the input element.
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::getHTML()
     */
    public function getHTML() : string
    {
        $this->processFlags();
        $this->setSize();
        $strHTML = $this->buildContainerDiv();

        $this->strID = $this->strID ?: $this->strName;

        $strHTML .= '<input';
        $strHTML .= ' type="' . $this->strType . '"';
        $strHTML .= ' name="' . $this->strName . '"';
        $strHTML .= $this->buildClass();
        $strHTML .= $this->buildID();
        $strHTML .= $this->buildStyle();
        $strHTML .= $this->buildTabindex();
        $strHTML .= $this->buildValue();
        $strHTML .= $this->buildAttributes();
        $strHTML .= $this->buildListLink();
        $strHTML .= '>';

        $strHTML .= $this->buildSelectButton();
        $strHTML .= $this->buildSuffix();
        $strHTML .= $this->buildDatalist();

        $strHTML .= '</div>' . PHP_EOL;

        return $strHTML;
    }

    /**
     * Set the tab index of the element.
     * Method is called from the PageGenerator after an element is added to the form.
     * @param int $iTabindex
     * @return int the number of indexes, the element needs
     */
    public function setTabindex(int $iTabindex) : int
    {
        if ($this->oFlags->isSet(FormFlags::HIDDEN | FormFlags::READ_ONLY | FormFlags::DISABLED)) {
            return 0;
        }
        $this->iTabindex = $iTabindex;
        return 1;
    }

    /**
     * Process the current flags before the HTML is generated.
     */
    protected function processFlags() : void
    {
        $this->setTypeFromFlags();

        if ($this->oFlags->isSet(FormFlags::MANDATORY)) {
            $this->addAttribute('required');
        }
        if ($this->oFlags->isSet(FormFlags::READ_ONLY)) {
            $this->addAttribute('readonly');
        } else if ($this->oFlags->isSet(FormFlags::DISABLED)) {
            $this->addAttribute('disabled');
        }
    }

    /**
     * Set the type depending on some flags
     */
    protected function setTypeFromFlags() : void
    {
        if ($this->oFlags->isSet(FormFlags::HIDDEN)) {
            $this->strType = 'hidden';
        }
        if ($this->oFlags->isSet(FormFlags::PASSWORD)) {
            $this->strType = 'password';
        }
        if ($this->oFlags->isSet(FormFlags::FILE)) {
            $this->strType = 'file';
            if ($this->oFlags->isSet(FormFlags::HIDDEN)) {
                $this->addStyle('visibility', 'hidden');
            }
        }
    }

    /**
     * Set the size of the element.
     * If property $size contains numeric value, the HTML attrib 'size' is set, in case of a
     * string a width information including dimension (px, em, ...) is assumed.
     *
     */
    protected function setSize() : void
    {
        if ($this->oFlags->isSet(FormFlags::HIDDEN)) {
            $this->size = '';
        }
        if ((is_numeric($this->size)) && ($this->size > 0)) {
            $this->addAttribute('size', (string)$this->size);
        } else if (!empty($this->size)) {
            // size given as string including dimension
            $this->addStyle('width', $this->size);
        }
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::buildClass()
     */
    protected function buildClass() : string
    {
        if (!empty($this->strClass)) {
            $this->strClass .= ' ';
        }
        $this->strClass .= ($this->oFlags->isSet(FormFlags::MANDATORY)) ? ' inputMand' : ' inputOK';
        if ($this->oFlags->isSet(FormFlags::ALIGN_RIGHT)) {
            $this->strClass .= '_R';
        }
        return parent::buildClass();
    }

    /**
     * Build the markup for a suffix succeeding the input element.
     * @return string
     */
    protected function buildSuffix() : string
    {
        $strHTML = '';
        if (!empty($this->strSuffix)) {
            if ($this->oFlags->isSet(FormFlags::READ_ONLY)) {
                $strHTML .= '&nbsp;<span class="readonly">' . $this->strSuffix . '</span>';
            } else {
                $strHTML .= '&nbsp;' . $this->strSuffix;
            }
        }
        return $strHTML;
    }

    /**
     * Build attrib for associated datalist.
     * If the dataprovider contains a datalist in the selectoptions with the same name
     * as the element, we add the attrib to conect to this list.
     * @return string
     */
    protected function buildListLink() : string
    {
        $strLink = '';
        $aOptions = $this->oFG->getData()->getSelectOptions($this->strName);
        if (count($aOptions) > 0) {
            $strLink = ' list="list' . $this->strName . '"';
        }
        return $strLink;
    }

    /**
     * Build the markup for associated datalist.
     * If the dataprovider contains a datalist in the selectoptions with the same name
     * as the element, we build this datalist.
     * @return string
     */
    protected function buildDatalist() : string
    {
        $strHTML = '';
        $aOptions = $this->oFG->getData()->getSelectOptions($this->strName);
        if (count($aOptions) > 0) {
            $strHTML .= '<datalist id="list' . $this->strName . '">' . PHP_EOL;
            foreach ($aOptions as $strValue) {
                $strHTML .= '    ';
                $strHTML .= '<option ';
                $strHTML .= 'value="' . $strValue . '">' . PHP_EOL;
            }
            $strHTML .= '</datalist>' . PHP_EOL;
        }
        return $strHTML;
    }

    /**
     * Build the markup for the select button(s).
     * If input  is set to readonly, an additional 'delete' button is appended.
     * @param string $strCssClass
     * @return string
     */
    protected function buildSelectButton(string $strCssClass = 'picker') : string
    {
        $wButtonFlags = $this->oFlags->getButtonFlags();
        if ($wButtonFlags === 0 || $this->oFlags->isSet(FormFlags::HIDDEN)) {
            return '';
        }

        $strImg = '';
        $strTitle = '';
        $strOnClick = '';
        $strID = '';

        // only one of the button flags is allowed - so we can use switch-case!
        switch ($wButtonFlags) {
        case FormFlags::ADD_DTU:
            $strUsername = $this->oFG->getData()->getValue('username');
            [$strImg, $strTitle] = $this->oFG->getStdImage(FormImage::IMG_DTU);
            $strOnClick = "onInsertDateTimeUser('" . $this->strName . "', '" . $strUsername . "')";
            $strID = $this->strName . 'DTU';
            break;
        case FormFlags::ADD_DATE_PICKER:
            [$strImg, $strTitle] = $this->oFG->getStdImage(FormImage::IMG_DATE_PICKER);
            break;
        case FormFlags::ADD_TIME_PICKER:
            [$strImg, $strTitle] = $this->oFG->getStdImage(FormImage::IMG_TIME_PICKER);
            break;
        case FormFlags::BROWSE_SERVER:
            [$strImg, $strTitle] = $this->oFG->getStdImage(FormImage::IMG_BROWSE);
            $strOnClick = "browseServer('" . $this->strName . "','','" . $this->strExpandFolder . "')";
            $strID = $this->strName . 'BS';
            break;
        case FormFlags::ADD_SELBTN:
            [$strImg, $strTitle] = $this->oFG->getStdImage(FormImage::IMG_SEARCH);
            $strOnClick = "onSelect('" . $this->strName . "')";
            $strID = $this->strName . 'SB';
            $strImg = $this->strSelectImg ?: $strImg;
            $strTitle = $this->strSelectImgTitle ?: $strTitle;
            break;
        default:
            trigger_error('Only one of the button-flags can be set!', E_USER_ERROR);
        }
        $strHTML = $this->buildSelectImage($strImg, $strTitle, $strOnClick, $strID, $strCssClass);
        if (!empty($strHTML) && $this->oFlags->isSet(FormFlags::READ_ONLY)) {
            [$strImg, $strTitle] = $this->oFG->getStdImage(FormImage::IMG_DELETE);
            $strOnClick = "resetElement('" . $this->strName . "')";
            $strID = $this->strName . 'Del';
            $strHTML .= $this->buildSelectImage($strImg, $strTitle, $strOnClick, $strID, $strCssClass);
        }
        return $strHTML;
    }

    /**
     * Build the markup for a selectimage.
     * @param string $strImg
     * @param string $strTitle
     * @param string $strOnClick
     * @param string $strID
     * @param string $strCssClass
     * @return string
     */
    protected function buildSelectImage(string $strImg, string $strTitle, string $strOnClick, string $strID, string $strCssClass) : string
    {
        $strHTML = '';
        if (!empty($strImg)) {
            $strHTML = '<img class="' . $strCssClass . '" src="' . $strImg . '" alt="[?]"';
            if (!empty($strID)) {
                $strHTML .= ' id="' . $strID . '"';
            }
            if (!empty($strTitle)) {
                $strHTML .= ' title="' . $strTitle . '"';
            }
            if (!empty($strOnClick)) {
                $strHTML .= ' onclick="' . $strOnClick . '"';
            }
            $strHTML .= '>';
        }
        return $strHTML;
    }
}
