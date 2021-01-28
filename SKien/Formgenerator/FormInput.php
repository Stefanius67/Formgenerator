<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Base-class for all elements intended to get user input.
 *
 * #### History
 * - *2020-05-12*   initial version
 * - *2021-01-07*   PHP 7.4
 *
 * @package Formgenerator
 * @version 1.1.0
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormInput extends FormElement
{
    /** @var string value input type    */
    protected string $strType;
    /** @var int|string size as number or as string including dimension ('%', 'px', 'em') */
    protected $size;
    /** @var string image displayed, if selectbutton is enabled     */
    protected string $strSelectImg;
    /** @var string tooltip for selectbutton     */
    protected string $strSelectImgTitle;
    /** @var string additional info for BrowseServer - Button     */
    protected string $strBrowseServer;
    /** @var string suffix directly after the element     */
    protected string $strSuffix;
    
    /**
     * @param string $strName Name (also used as ID, if not set separate)
     * @param int|string $size number set the size-attribute, a string is used for the width attribute
     * @param int $wFlags       
     */
    public function __construct(string $strName, $size, int $wFlags = 0) 
    {
        parent::__construct($wFlags);
        $this->strName = $strName;
        $this->size = $size;
        $this->strType = 'text';
        $this->strSelectImg = '';
        $this->strSelectImgTitle = '';
        $this->strBrowseServer = '';
        $this->strSuffix = '';
        
        $this->addFlags($wFlags);
    }
    
    /**
     * Add flags to existing element. 
     * Maybe overloaded in derived class(es) 
     * @param int $wFlags
     * 
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::addFlags()
     */
    public function addFlags($wFlags) : void
    {
        $this->oFlags->add($wFlags);
    }
    
    /**
     * set image and title for select-button (leave strImg blank for default)
     * @param string $strImg
     * @param string $strTitle (default = '')
     */
    public function setSelectImg(string $strImg, string $strTitle = '') : void
    {
        $this->strSelectImg = $strImg;
        $this->strSelectImgTitle = $strTitle;
    }
    
    /**
     * @param string $strBrowseServer
     */
    public function setBrowseServer(string $strBrowseServer) : void
    {
        $this->strBrowseServer = $strBrowseServer;
    }
    
    /**
     * @param string $strSuffix
     */
    public function setSuffix(string $strSuffix) : void
    {
        $this->strSuffix = $strSuffix;
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
        $strHTML .= '>';
        
        // some additional elements
        if ($this->oFlags->isSet(FormFlags::ADD_DTU)) {
            $strHTML .= '<img class="picker" src="' . $this->getStdImage(self::IMG_DTU) . '"  alt="[X]"';
            $strHTML .= ' id="' . $this->strName . 'DTU"';
            $strHTML .= ' title="aktuelle Datum Uhrzeit / Benutzername eintragen"';
            $strHTML .= ' onclick="OnInsertDateTimeUser(' . "'" . $this->strName . "'" . ');">';
        } else if ($this->oFlags->isSet(FormFlags::ADD_DATE_PICKER)) {
            $strHTML .= '<img class="picker" src="' . $this->getStdImage(self::IMG_DATE_PICKER) . '" alt="[X]"';
            $strHTML .= ' id="' . $this->strName . 'DP"';
            $strHTML .= ' title="Datum auswählen"';
            $strHTML .= ' onclick="OnDatePicker(' . "'" . $this->strName . "'" . ');">';
        } else if ($this->oFlags->isSet(FormFlags::ADD_TIME_PICKER)) {
            $strHTML .= '<img class="picker" src="' . $this->getStdImage(self::IMG_TIME_PICKER) . '"  alt="[X]"';
            $strHTML .= ' id="' . $this->strName . 'TP"';
            $strHTML .= ' title="Uhrzeit auswählen"';
            $strHTML .= ' onclick="OnTimePicker(' . "'" . $this->strName . "'" . ');">';
        }
        $strHTML .= $this->buildSelectImage();
        $strHTML .= $this->buildSuffix();
        
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
     * Build the markup for a select button.
     * @param string $strClass
     * @return string
     */
    protected function buildSelectImage(string $strClass = 'picker') : string
    {
        $strHTML = '';
        if ($this->oFlags->isSet(FormFlags::ADD_SELBTN)) {
            $strImg = $this->strSelectImg;
            $strTitle = $this->strSelectImgTitle;
            if (empty($strImg)) {
                $strImg = $this->getStdImage(self::IMG_SEARCH);
            }
            $strHTML .= '<img class="' . $strClass . '" src="' . $strImg . '" alt="Auswahl"';
            if (!empty($strTitle)) {
                $strHTML .= ' title="' . $strTitle . '"';
            }
            if (!empty($this->strBrowseServer)) {
                $strHTML .= " onclick=\"BrowseServer('" . $this->strName . "','','" . $this->strBrowseServer . "');\">";
                if ($this->oFlags->isSet(FormFlags::READ_ONLY)) {
                    $strHTML .= '<img class="picker" src="' . $this->getStdImage(self::IMG_DELETE) . '" alt="L&ouml;schen" title="L&ouml;schen" ';
                    $strHTML .= " onclick=\"ResetInput('" . $this->strName . "');\">";
                }
            } else {
                $strHTML .= ' onclick="OnSelect(' . "'" . $this->strName . "'" . ');">';
            }
        }
        return $strHTML;
    }
}
