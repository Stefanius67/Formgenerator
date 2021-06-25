<?php
declare(strict_types=1);

namespace SKien\Formgenerator;

/**
 * An instance of this class is the starting point of each form based on XML definition.
 *
 * @see FormGenerator
 * @package Formgenerator
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class XMLForm extends FormGenerator
{
    /** no xml/xsd error occurred    */
    const E_OK = 0;
    /** the specified file does not exist   */
    const E_FILE_NOT_EXIST = 1;
    /** any xml syntax error    */
    const E_XML_ERROR = 2;
    /** error while xsd schema validation   */
    const E_XSD_ERROR = 3;
    /** the root element &lt;FormGenerator&gt; is missing   */
    const E_MISSING_ROOT = 4;
    /** no &lt;Form&gt; element found   */
    const E_MISSING_FORM = 5;
    /** an unknown form element found   */
    const E_UNKNOWN_FORM_ELEMENT = 6;

    /** @internal the XSD schema to validate against     */
    const XML_SCHEMA = 'FormGenerator.xsd';

    /** @var string error message     */
    protected string $strErrorMsg = '';
    /** @var bool error message as plain text (\n instead of &lt;br/&gt;) */
    protected bool $bPlainError = false;
    /** @var bool check xml file against the FormGenerator XSD schema */
    protected bool $bSchemaValidate = true;

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
        if (!file_exists($strXMLFile)) {
            // if file not exist, there is nothing more to do...
            $this->strErrorMsg = 'Missing form file: ' . $strXMLFile;
            return self::E_FILE_NOT_EXIST;
        }
        // to get more detailed information about XML errors and XML schema validation
        libxml_use_internal_errors(true);
        $iResult = self::E_XML_ERROR;
        $oXMLForm = new \DOMDocument();
        if ($oXMLForm->load($strXMLFile)) {
            // the XML schema is allways expected in the same directory as the XML file itself
            $strXSDFile = (string)pathinfo($strXMLFile, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . self::XML_SCHEMA;
            $oRoot = $oXMLForm->documentElement;
            if ($this->bSchemaValidate && !$oXMLForm->schemaValidate($strXSDFile)) {
                $iResult = self::E_XSD_ERROR;
            } elseif ($oRoot->nodeName != 'FormGenerator') {
                $this->strErrorMsg = 'Missing document root &lt;FormGenerator&gt; in form file: ' . $strXMLFile;
                $iResult = self::E_MISSING_ROOT;
            } elseif (($oForm = $this->getXMLChild($oRoot, 'Form')) === null) {
                $this->strErrorMsg = 'Missing form elemnt &lt;Form&gt; as first child of the root: ' . $strXMLFile;
                $iResult = self::E_MISSING_FORM;
            } else {
                // First we read some general infos for the form
                $this->readAdditionalXML($oForm);

                // and iterate recursive through the child elements
                $iResult = $this->createChildElements($oForm, $this);
            }
        }
        if ($iResult != self::E_OK && strlen($this->strErrorMsg) == 0) {
            $strErrorMsg = ($iResult != self::E_XSD_ERROR ? 'XML error' : 'XSD Schema validation error');
            $this->strErrorMsg .= $this->getFormatedXMLError($strErrorMsg, $this->bPlainError);
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
            if (strtolower($oXMLChild->nodeName) == '#text' || strtolower($oXMLChild->nodeName) == '#comment' || !($oXMLChild instanceof \DOMElement)) {
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
                $this->strErrorMsg = 'Unknown form element: ' . $oXMLChild->nodeName;
                $iResult = self::E_UNKNOWN_FORM_ELEMENT;
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

    /**
     * enable/disable the schema validation (default set to true)
     * @param bool $bSchemaValidate
     */
    public function setSchemaValidation(bool $bSchemaValidate) : void
    {
        $this->bSchemaValidate = $bSchemaValidate;
    }

    /**
     * Get formated list of detailed XML error.
     * this method only works, if <i><b>libxml_use_internal_errors(true);</b></i> is called
     * before parsing the xml and/or validating the xml against XSD file.
     * @param bool $bPlainText
     * @return string
     */
    protected function getFormatedXMLError(string $strError, bool $bPlainText) : string
    {
        $strErrorMsg = '';
        $errors = libxml_get_errors();
        $aLevel = [LIBXML_ERR_WARNING => 'Warning ', LIBXML_ERR_ERROR => 'Error ', LIBXML_ERR_FATAL => 'Fatal Error '];
        if ($bPlainText) {
            $strErrorMsg = $strError . ': ';
            foreach ($errors as $error) {
                $strErrorMsg .= PHP_EOL . $aLevel[$error->level] . $error->code;
                $strErrorMsg .= ' (Line ' . $error->line . ', Col ' . $error->column . ') ';
                $strErrorMsg .= PHP_EOL . trim($error->message);
            }
        } else {
            $strErrorMsg = '<h2>' . $strError . '</h2>';
            foreach ($errors as $error) {
                $strErrorMsg .= '<h3>' . $aLevel[$error->level] . $error->code . '</h3>';
                $strErrorMsg .= '<ul><li>Line: ' . $error->line . '</li><li>Col: ' . $error->column . '</li></ul>';
                $strErrorMsg .= '<p>' . trim($error->message) . '</p>';
            }
        }
        return $strErrorMsg;
    }
}
