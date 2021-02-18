<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Class to insert a script tag inside of the form.  
 *
 * @package Formgenerator
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormScript extends FormElement
{
    /** @var string text for the line label     */
    protected string $strScript;
    
    public function __construct(string $strScript)
    {
        parent::__construct(0);
        $this->strScript = $strScript;
    }

    /**
     * Insert the script at current position of the form.
     * @return string
     */
    public function getHTML() : string
    {
        $strHTML  = '<script>' . $this->strScript . '</script>' . PHP_EOL;
        return $strHTML;
    }
}
