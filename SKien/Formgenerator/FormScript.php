<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Class to insert a script tag inside of the form.  
 *
 * #### History
 * - *2021-02-12*   initial version
 *
 * @package Formgenerator
 * @version 1.1.0
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
     * Just insert the script at current position of the form.
     * @return string
     */
    public function getHTML() : string
    {
        $strHTML  = '<script>' . $this->strScript . '</Script>' . PHP_EOL;
        return $strHTML;
    }
}
