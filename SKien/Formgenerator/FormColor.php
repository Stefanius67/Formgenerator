<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * Element to select Color.
 * Uses the JSColor - picker version 2.4.5  (https://jscolor.com/)
 * 
 * The jscolor.js or jscolor.min.js mujst be included in the page.
 * <br/><br/>
 * <b>!!! Take care of the JSColor licence conditions if using this control !!!</b>
 *
 * #### History
 * - *2020-05-12*   initial version
 * - *2021-01-07*   PHP 7.4
 *
 * @package Formgenerator
 * @version 1.1.0
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 * @link https://jscolor.com/
 */
class FormColor extends FormInput
{
    /**
     * Creates input field for color values.
     * @param string $strName
     * @param int|string $size number set the size-attribute, a string is used for the width attribute
     * @param int $wFlags    default 0
     */
    public function __construct(string $strName, $size = 6, int $wFlags = 0) 
    {
        parent::__construct($strName, $size, $wFlags);
        $this->bCreateScript = true;
        $this->addAttribute('data-jscolor', '{}');
    }
    
    /**
     * {@inheritDoc}
     * @see \SKien\Formgenerator\FormElement::getScript()
     */
    public function getScript() : string
    {
        $strScript = '';
        
        if ($this->oFG->getDebugMode()) {
            // in debug environment we give alert if scriptfile is missing!
            $strScript  = "if (typeof jscolor === 'undefined') {";
            $strScript .= "    alert('You must include <jscolor.js> to use the FormColor input element!');";
            $strScript .= "}" . PHP_EOL;
        }

        // If some presets for the picker are defined in the config, just set it:
        // - position (default: bottom)
        // - border color (no default value - set only if defined in the config)
        // - background color (no default value - set only if defined in the config)
        // - palette (no default value - set only if defined in the config)
        $aPreset = ['position' => $this->oFG->getConfig()->getString('Color.position', 'bottom')];
        if (($strBorderColor = $this->oFG->getConfig()->getString('Color.borderColor')) != '') {
            $aPreset["borderColor"] = $strBorderColor;
        }
        if (($strBackgroundColor = $this->oFG->getConfig()->getString('Color.backgroundColor')) != '') {
            $aPreset["backgroundColor"] = $strBackgroundColor;
        }
        $aPalette = $this->oFG->getConfig()->getArray('Color.palette');
        if (count($aPalette) > 0) {
            $aPreset['palette'] = $aPalette;
        }
        $strScript .= 'jscolor.presets.default = ' . json_encode($aPreset) . PHP_EOL;
        
        return $strScript;
    }
}
