<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Class to display a header line inside of the form.
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
class FormHeader extends FormElement
{
    /** @var string text for the header     */
    protected string $strText;
    /** @var int level of the HTML header element     */
    protected int $iLevel; 
    
    /**
     * Create header element (<h2> header)
     * @param string $strText
     * @param number $iLevel
     */
    public function __construct(string $strText, $iLevel = 2) 
    {
        $this->strText = $strText;
        $this->iLevel = $iLevel;
    }
    
    /**
     * Build the HTML-notation for the header text
     * @return string
     */
    public function getHTML() : string
    {
        $strHTML = '<h' . $this->iLevel . '>' . $this->strText . '</h' . $this->iLevel . '>' . PHP_EOL;
        return $strHTML;
    }
}
