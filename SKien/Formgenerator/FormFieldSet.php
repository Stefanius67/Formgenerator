<?php
namespace SKien\Formgenerator;

/**
 * Class to create fieldset as parent of lines.
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
class FormFieldSet extends FormElement
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
     * @param string $strID
     * @param int $iType
     */
    public function __construct(string $strLegend, string $strID='', int $iType = self::TEXT) 
    {
        $this->strLegend = $strLegend;
        $this->strID = $strID;
        $this->iType = $iType;
        $this->iImageHeight = -1;
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
     * Add new line to this fieldset
     * @param string $strLabel (default: '&nbsp;')
     * @return \SKien\Formgenerator\FormLine
     */
    public function addLine(string $strLabel='&nbsp;') : FormLine
    {
        $oFL = new FormLine($strLabel);
        $this->add($oFL);
        
        return $oFL;
    }

    /**
     * Build the HTML-notation for the fieldset.
     * @return string
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
