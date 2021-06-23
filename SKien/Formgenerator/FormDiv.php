<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 *  Represents a &lt;div&gt; inside of the form.
 *  Div's can be used to arrange some fieldset horizontally or to group some
 *  elements for JS hide/show operations.
 *
 *  An example of arranging two div / fieldsets horizontally and adjusting the
 *  height of the two can be found in ColumnForm.php and ColumnFormXML.php in
 *  the example directory.
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormDiv extends FormCollection
{
    /** 'normal' div with no float style */
    const   NONE    = -1;
    /** Use `CLEAR` div after one or more floating divs */
    const   CLEAR   = 0;
    /** Left floating div */
    const   LEFT    = 1;
    /** Right floating div */
    const   RIGHT   = 2;

    /** @var int width of the div in percent     */
    protected int  $iWidth;
    /** @var int align of the div (FormDiv::NONE, FormDiv::LEFT, FormDiv::RIGHT, FormDiv::CLEAR)     */
    protected int  $iAlign;

    /**
     * Create a Div element.
     * @param int $iWidth   width of the div in %
     * @param int $iAlign   align of the div (FormDiv::NONE, FormDiv::LEFT, FormDiv::RIGHT, FormDiv::CLEAR)
     */
    public function __construct(int $iWidth = 0, int $iAlign = self::CLEAR)
    {
        parent::__construct(0);
        $this->iAlign = $iAlign;
        $this->iWidth = $iWidth;
        if ($this->iWidth > 0) {
            $this->addStyle('width', $this->iWidth . '%');
        }
        switch ($this->iAlign) {
        case self::CLEAR:
            $this->addStyle('clear', 'both');
            break;
        case self::LEFT:
            $this->addStyle('float', 'left');
            break;
        case self::RIGHT:
            $this->addStyle('float', 'right');
            break;
        case self::NONE:
        default:
            break;
        }
    }

    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::fromXML()
     * @internal
     */
    static public function fromXML(\DOMElement $oXMLElement, FormCollection $oFormParent) : ?FormElement
    {
        $iAlign = self::CLEAR;
        $strAlign = self::getAttribString($oXMLElement, 'align', 'CLEAR');
        $strConstName = 'self::' . strtoupper($strAlign);
        if (defined($strConstName)) {
            $iAlign = constant($strConstName);
        } else {
            trigger_error('Unknown Constant [' . $strConstName . '] for the Div-Alignment property!', E_USER_ERROR);
        }
        $iWidth = self::getAttribInt($oXMLElement, 'width', 0);
        $oFormElement = new self($iWidth, $iAlign);
        $oFormParent->add($oFormElement);
        $oFormElement->readAdditionalXML($oXMLElement);

        return $oFormElement;
    }

    /**
     * Build the HTML-notation for the div element.
     * @internal
     */
    public function getHTML() : string
    {
        $strHTML  = '';
        $strHTML .= '<div';
        $strHTML .= $this->buildID();
        $strHTML .= $this->buildStyle();
        $strHTML .= $this->buildAttributes();
        $strHTML .= ">" . PHP_EOL;
        $iCnt = count($this->aChild);
        for ($i = 0; $i < $iCnt; $i++) {
            $strHTML .= $this->aChild[$i]->GetHTML();
        }
        $strHTML .= '</div>' . PHP_EOL;
        return $strHTML;
    }
}

