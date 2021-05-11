<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * 
 *
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class XMLForm extends FormGenerator
{
    const E_OK = 0;
    const E_FILE_NOT_EXIST = 1;
    const E_INVALID_FORM = 2;
    const E_INVALID_FORM_ELEMENT = 3;
    
    /** @var string error message     */
    protected string $strErrorMsg = '';
    /** @var bool error message as plain text (\n instead of &lt;br/&gt;) */
    protected bool $bPlainError = false;
    
    /**
     * no constructor - always use the constructor of the parent! 
     */

    /**
     * Load form from the given XML file. 
     * @param string $strXMLFile
     * @return int
     */
    public function loadXML(string $strXMLFile) : int
    {
        $iResult = self::E_OK;
        if (!file_exists($strXMLFile)) {
            $this->strErrorMsg = 'Missing form file: ' . $strXMLFile;
            return self::E_FILE_NOT_EXIST;
        }
        libxml_use_internal_errors(true);
        $oXMLForm = new \DOMDocument();
        if ($oXMLForm->load($strXMLFile)) {
            $oRoot = $oXMLForm->documentElement;
            if ($oRoot->nodeName != 'Form') {
                $this->strErrorMsg = 'Missing document root &lt;Form&gt; in form file: ' . $strXMLFile;
                return self::E_INVALID_FORM;
            }
            // First we read some general infos for the form
            $this->readAdditionalXML($oRoot);
            
            // and iterate recursive through the child elements
            $this->createChildElements($oRoot, $this);
        } else {
            $this->strErrorMsg = 'XML Parser Error: ';
            $errors = libxml_get_errors();
            $aLevel = [LIBXML_ERR_WARNING => 'Warning ', LIBXML_ERR_ERROR => 'Error ', LIBXML_ERR_FATAL => 'Fatal Error '];
            $strCR = ($this->bPlainError ? PHP_EOL : '<br/>');
            foreach ($errors as $error) {
                $this->strErrorMsg .= $strCR . $aLevel[$error->level] . $error->code;
                $this->strErrorMsg .= ' (Line ' . $error->line . ', Col ' . $error->column . ') ';
                $this->strErrorMsg .= trim($error->message);
            }
            $iResult = self::E_INVALID_FORM;
        }
        libxml_clear_errors();
        return $iResult;
    }

    /**
     * Iterate through all childs of the given parent node and create the according element.
     * This method cals itself recursive for all collection elements. 
     * @param \DOMElement $oXMLParent the DOM Element containing the infos for the formelement to create
     * @param FormCollection $oFormParent the parent element in the form
     * @return int
     */
    protected function createChildElements(\DOMElement $oXMLParent, FormCollection $oFormParent) : int
    {
        $iResult = self::E_OK;
        if (!$oXMLParent->hasChildNodes()) {
            return $iResult;
        }
        foreach ($oXMLParent->childNodes as $oXMLChild) {
            if (strtolower($oXMLChild->nodeName) == '#text') {
                continue;
            }
            $strClassname = __NAMESPACE__ . '\Form' . $oXMLChild->nodeName;
            if (class_exists($strClassname) && is_subclass_of($strClassname, __NAMESPACE__ . '\FormElement')) {
                $oFormElement = $strClassname::fromXML($oXMLChild, $oFormParent);
                // recursive call for collection element (div, fieldset, line)
                if ($oFormElement instanceof FormCollection) {
                    $iResult = $this->createChildElements($oXMLChild, $oFormElement);
                }
            } else {
                $iResult = self::E_INVALID_FORM_ELEMENT;
            }
            if ($iResult != self::E_OK) {
                break;
            }
        }
        return $iResult;
    }
    
    /**
     * Get message to error occured.
     * May contain multiple lines in case of any XML formating errors. 
     * @return string
     */
    public function getErrorMsg() : string
    {
        return $this->strErrorMsg;
    }
    
    /**
     * Format error message as plain text (\n instead of <br/> for multiline errors)
     * @param bool $bPlainError
     */
    public function setPlainError(bool $bPlainError) : void
    {
        $this->bPlainError = $bPlainError;
    }
}
