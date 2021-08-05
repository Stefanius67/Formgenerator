<?php
declare(strict_types=1);

namespace SKien\Test;

use PHPUnit\Framework\TestCase;

/**
 * Extension of PHPUnit Testcase for HTML testing.
 * This class supports several methods to test/assert for
 *  - valid HTML5 document/block
 *  - existing HTML tag/attribute
 *  - HTML tag/attribute value equals expected
 *  - HTML tag/attribute contains expected part
 *  - given text is plain text (means, text doesn't contain any HTML tags)
 *
 * <b>To test for valid HTML5 doc/block the PHP libraries cURL and OpenSSL required.</b>
 *
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class HtmlTestCase extends TestCase
{
    /** Ansi escape code color red  */
    const AEC_RED       = "\e[1;31m";
    /** Ansi escape code default  */
    const AEC_DEFAULT   = "\e[0m";
    
    /**
     * Disable libxml errors and allow user to fetch error information as needed
     */
    public static function setUpBeforeClass() : void
    {
        libxml_use_internal_errors(true);
    }

    /**
     * Assert that the given string is a valid HTML5 document.
     * Calls https://about.validator.nu through cURL request.
     * More info can be found on https://github.com/validator/validator/wiki/Service-%C2%BB-HTTP-interface
     * @param string $strHTML The HTML to validate
     */
    public function assertValidHtml(string $strHTML, string $strMessage = null) : void
    {
        // cURL
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://html5.validator.nu/',
            CURLOPT_HTTPHEADER     => ['User-Agent: cURL'],
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_POST           => TRUE,
            CURLOPT_POSTFIELDS     => [
                'out'     => 'json',
                'content' => $strHTML,
            ],
        ]);
        $strResponse = curl_exec($curl);
        if (!$strResponse) {
            $this->markTestIncomplete('Issues checking HTML validity. (cURL-Error: ' . curl_error($curl) . ')');
        }
        curl_close($curl);
        
        $oResponse = json_decode($strResponse);
        $strStart = "HTML validation failed:";
        $strError = '';
        foreach ($oResponse->messages as $oMessage) {
            if ($oMessage->type === 'error') {
                $strError .= $strStart . PHP_EOL . '-> ' . $oMessage->message;
                $strStart = '';
            }
        }
        if (strlen($strError) > 0) {
            $strError = $this->getFormatedError($strError);            $this->fail($strMessage ?? $strError);
        }
        
        // valid HTML
        $this->assertTrue(true);
    }

    /**
     * Assert that the given string is a valid HTML5 block.
     * The HTML block is enclosed in a valid HTML5 Document definition before
     * validation.
     * @param string $strHTML The HTML to validate
     */
    public function assertValidHtmlBlock(string $strHTML, string $strMessage = null) : void
    {
        $this->assertValidHtml('<!DOCTYPE html><html><head><title>Test</title></head><body>' . $strHTML . '</body></html>', $strMessage);
    }
    
    /**
     * Assert that the given text don't contain any HTML tags (-> contains plain text).
     * @param string $text  The text to test
     */
    public function assertContainsNoHtmlTag(string $text, string $strMessage = null) : void
    {
        if($text != strip_tags($text)) {
            $this->fail($strMessage ?? 'Text contains HTML tags.');
        }
        $this->assertTrue(TRUE);
    }
    
    /**
     * Assert that the given HTML string contains the requested tag.
     * @param string $strHTML      The HTML to validate
     * @param string $strTag       The Tag we are locking for
     * @param string $strMessage   Optional Message
     */
    public function assertHtmlHasTag(string $strHTML, string $strTag, string $strMessage = null) : void
    {
        $this->getItemByTagName($strHTML, $strTag, $strMessage);

        // HTML tag found
        $this->assertTrue(true);
    }

    /**
     * Assert that the given HTML string contains the requested tag with expected value.
     * @param string $strHTML      The HTML to validate
     * @param string $strTag       The Tag we are locking for
     * @param string $strExpect    Expected node value
     * @param string $strMessage   Optional Message
     */
    public function assertHtmlTagEquals(string $strHTML, string $strTag, string $strExpect, string $strMessage = null) : void
    {
        $oItem = $this->getItemByTagName($strHTML, $strTag, $strMessage);
        if ($oItem->nodeValue !== $strExpect) {
            $strError = $this->getFormatedError('“' . $strTag . '” value: "' . $oItem->nodeValue . '",  expected: "' . $strExpect . '"');
            $this->fail($strMessage ?? $strError);
        }
        // HTML tag value equal expexted
        $this->assertTrue(true);
    }

    /**
     * Assert that the given HTML string contains the requested tag containing expected part.
     * @param string $strHTML      The HTML to validate
     * @param string $strTag       The Tag we are locking for
     * @param string $strContains  Part the node value must contain
     * @param string $strMessage   Optional Message
     */
    public function assertHtmlTagContains(string $strHTML, string $strTag, string $strContains, string $strMessage = null) : void
    {
        $oItem = $this->getItemByTagName($strHTML, $strTag, $strMessage);
        if (strpos($oItem->nodeValue, $strContains) === false) {
            $strError = $this->getFormatedError('“' . $strTag . '” value "' . $oItem->nodeValue . '" not containing "' . $strContains . '"');
            $this->fail($strMessage ?? $strError);
        }
        // HTML tag value equal expexted
        $this->assertTrue(true);
    }

    /**
     * Assert that the given HTML string contains the requested tag with specified attrib.
     * @param string $strHTML      The HTML to validate
     * @param string $strTag       The Tag we are locking for
     * @param string $strAttrib    Attrib to test
     * @param string $strMessage   Optional Message
     */
    public function assertHtmlTagHasAttrib(string $strHTML, string $strTag, string $strAttrib, string $strMessage = null) : void
    {
        $this->getTagAttribute($strHTML, $strTag, $strAttrib, $strMessage);
        $this->assertTrue(true);
    }

    /**
     * Assert that the given HTML string contains the requested tag with specified attrib having expected value.
     * @param string $strHTML      The HTML to validate
     * @param string $strTag       The Tag we are locking for
     * @param string $strAttrib    Attrib to test
     * @param string $strExpect    Expected attribute value
     * @param string $strMessage   Optional Message
     */
    public function assertHtmlTagAttribEquals(string $strHTML, string $strTag, string $strAttrib, string $strExpect, string $strMessage = null) : void
    {
        $strValue = $this->getTagAttribute($strHTML, $strTag, $strAttrib, $strMessage);
        if ($strValue !== $strExpect) {
            $strError = $this->getFormatedError('“' . $strTag . '”: [' . $strAttrib . '] = "' . $strValue . '", Expected: "' . $strExpect . '"');
            $this->fail($strMessage ?? $strError);
        }
        $this->assertTrue(true);
    }

    /**
     * Assert that the given HTML string contains the requested tag with specified attrib containing expected part.
     * @param string $strHTML      The HTML string
     * @param string $strTag       The Tag we are locking for
     * @param string $strAttrib    Attrib to test
     * @param string $strContains  Part the attrib value must contain
     * @param string $strMessage   Optional Message
     */
    public function assertHtmlTagAttribContains(string $strHTML, string $strTag, string $strAttrib, string $strContains, string $strMessage = null) : void
    {
        $strValue = $this->getTagAttribute($strHTML, $strTag, $strAttrib, $strMessage);
        if (strpos($strValue, $strContains) === false) {
            $strError = $this->getFormatedError('“' . $strTag . '”: [' . $strAttrib . '] = "' . $strValue . '" not containing "' . $strContains . '"');
            $this->fail($strMessage ?? $strError);
        }
        $this->assertTrue(true);
    }
    
    /**
     * Assert that the given HTML string contains the requested tag with specified style having expected value.
     * @param string $strHTML      The HTML string
     * @param string $strTag       The Tag we are locking for
     * @param string $strStyle     The style to test
     * @param string $strValue     The expected value
     * @param string $strMessage   Optional Message
     */
    public function assertHtmlTagHasStyle(string $strHTML, string $strTag, string $strStyle, string $strExpect, string $strMessage = null) : void
    {
        $strValue = $this->getTagAttribute($strHTML, $strTag, 'style', $strMessage);
        $aStyles = $this->parseStyle($strValue);        if (!isset($aStyles[$strStyle]) || $aStyles[$strStyle] != $strExpect) {
            $strValue = $aStyles[$strStyle] ?? '';
            $strError = $this->getFormatedError('“' . $strTag . '”: style [' . $strStyle . '] = "' . $strValue . '", Expected: "' . $strExpect . '"');
            $this->fail($strMessage ?? $strError);
        }
        $this->assertTrue(true);
    }
    
    /**
     * Assert that the given HTML string contains element with requested ID.
     * @param string $strHTML      The HTML to validate
     * @param string $strID        ID of the element we are locking for
     * @param string $strMessage   Optional Message
     */
    public function assertHtmlHasElement(string $strHTML, string $strID, string $strMessage = null) : void
    {
        $this->getItemById($strHTML, $strID, $strMessage);
        
        // HTML tag found
        $this->assertTrue(true);
    }
    
    /**
     * Assert that the given HTML string contains element with requested ID and expected value.
     * @param string $strHTML      The HTML to validate
     * @param string $strID        ID of the element we are locking for
     * @param string $strExpect    Expected node value
     * @param string $strMessage   Optional Message
     */
    public function assertHtmlElementEquals(string $strHTML, string $strID, string $strExpect, string $strMessage = null) : void
    {
        $oItem = $this->getItemById($strHTML, $strID, $strMessage);
        if ($oItem->nodeValue !== $strExpect) {
            $strError = $this->getFormatedError('Element id = "' . $strID . '" value: "' . $oItem->nodeValue . '",  expected: "' . $strExpect . '"');
            $this->fail($strMessage ?? $strError);
        }
        // HTML tag value equal expexted
        $this->assertTrue(true);
    }
    
    /**
     * Assert that the given HTML string contains element with requested ID containing expected part.
     * @param string $strHTML      The HTML to validate
     * @param string $strID        ID of the element we are locking for
     * @param string $strContains  Part the node value must contain
     * @param string $strMessage   Optional Message
     */
    public function assertHtmlElementContains(string $strHTML, string $strID, string $strContains, string $strMessage = null) : void
    {
        $oItem = $this->getItemById($strHTML, $strID, $strMessage);
        if (strpos($oItem->nodeValue, $strContains) === false) {
            $strError = $this->getFormatedError('Element id = "' . $strID . '" value "' . $oItem->nodeValue . '" not containing "' . $strContains . '"');
            $this->fail($strMessage ?? $strError);
        }
        // HTML tag value equal expexted
        $this->assertTrue(true);
    }
    
    /**
     * Assert that the given HTML string contains element with requested ID and specified attrib.
     * @param string $strHTML      The HTML to validate
     * @param string $strID        ID of the element we are locking for
     * @param string $strAttrib    Attrib to test
     * @param string $strMessage   Optional Message
     */
    public function assertHtmlElementHasAttrib(string $strHTML, string $strID, string $strAttrib, string $strMessage = null) : void
    {
        $this->getElementAttribute($strHTML, $strID, $strAttrib, $strMessage);
        $this->assertTrue(true);
    }
    
    /**
     * Assert that the given HTML string contains element with requested ID and specified attrib having expected value.
     * @param string $strHTML      The HTML to validate
     * @param string $strID       ID of the element we are locking for
     * @param string $strAttrib    Attrib to test
     * @param string $strExpect    Expected attribute value
     * @param string $strMessage   Optional Message
     */
    public function assertHtmlElementAttribEquals(string $strHTML, string $strID, string $strAttrib, string $strExpect, string $strMessage = null) : void
    {
        $strValue = $this->getElementAttribute($strHTML, $strID, $strAttrib, $strMessage);
        if ($strValue !== $strExpect) {
            $strError = $this->getFormatedError('Element id = "' . $strID . '": [' . $strAttrib . '] = "' . $strValue . '", Expected: "' . $strExpect . '"');
            $this->fail($strMessage ?? $strError);
        }
        $this->assertTrue(true);
    }
    
    /**
     * Assert that the given HTML string contains element with requested ID and specified attrib containing expected part.
     * @param string $strHTML      The HTML string
     * @param string $strID        ID of the element we are locking for
     * @param string $strAttrib    Attrib to test
     * @param string $strContains  Part the attrib value must contain
     * @param string $strMessage   Optional Message
     */
    public function assertHtmlElementAttribContains(string $strHTML, string $strID, string $strAttrib, string $strContains, string $strMessage = null) : void
    {
        $strValue = $this->getElementAttribute($strHTML, $strID, $strAttrib, $strMessage);
        if (strpos($strValue, $strContains) === false) {
            $strError = $this->getFormatedError('Element id = "' . $strID . '": [' . $strAttrib . '] = "' . $strValue . '" not containing "' . $strContains . '"');
            $this->fail($strMessage ?? $strError);
        }
        $this->assertTrue(true);
    }
    
    /**
     * Assert that the given HTML string contains element with requested ID and specified style having expected value.
     * @param string $strHTML      The HTML string
     * @param string $strID        ID of the element we are locking for
     * @param string $strStyle     The style to test
     * @param string $strValue     The expected value
     * @param string $strMessage   Optional Message
     */
    public function assertHtmlElementHasStyle(string $strHTML, string $strID, string $strStyle, string $strExpect, string $strMessage = null) : void
    {
        $strValue = $this->getElementAttribute($strHTML, $strID, 'style', $strMessage);
        $aStyles = $this->parseStyle($strValue);
        if (!isset($aStyles[$strStyle]) || $aStyles[$strStyle] != $strExpect) {
            $strValue = $aStyles[$strStyle] ?? '';
            $strError = $this->getFormatedError('Element id = "' . $strID . '": style [' . $strStyle . '] = "' . $strValue . '", Expected: "' . $strExpect . '"');
            $this->fail($strMessage ?? $strError);
        }
        $this->assertTrue(true);
    }
    
    protected function loadHTML(string $strHTML, string $strMessage = null) : \DOMDocument
    {
        $oDOM = new \DOMDocument();
        if ($oDOM->loadHTML($strHTML) === false) {
            $this->fail($strMessage ?? $this->getFormatedXMLError());
        }
        return $oDOM;
    }
    
    protected function getItemByTagName(string $strHTML, string $strTag, string $strMessage = null) : \DOMNode
    {
        $oDOM = $this->loadHTML($strHTML, $strMessage);
        $oTags = $oDOM->getElementsByTagName($strTag);
        if ($oTags->count() === 0) {
            $strError = $this->getFormatedError('HTML Tag “' . $strTag . '” not found!');
            $this->fail($strMessage ?? $strError);
        }
        return $oTags->item(0);
    }
    
    protected function getTagAttribute(string $strHTML, string $strTag, string $strAttrib, string $strMessage = null) : string
    {
        $oItem = $this->getItemByTagName($strHTML, $strTag, $strMessage);
        if ($oItem->hasAttribute($strAttrib) === false) {
            $strError = $this->getFormatedError('HTML Tag “' . $strTag . '” has no attribute  [' . $strAttrib . ']!');
            $this->fail($strMessage ?? $strError);
        }
        return $oItem->getAttribute($strAttrib);
    }
    
    protected function getItemById(string $strHTML, string $strID, string $strMessage = null) : \DOMNode
    {
        $oDOM = $this->loadHTML($strHTML, $strMessage);
        $oItem = $oDOM->getElementById($strID);
        if ($oItem === null) {
            $strError = $this->getFormatedError('HTML Element id = "' . $strID . '" not found!');
            $this->fail($strMessage ?? $strError);
        }
        return $oItem;
    }
    
    protected function getElementAttribute(string $strHTML, string $strID, string $strAttrib, string $strMessage = null) : string
    {
        $oItem = $this->getItemById($strHTML, $strID, $strMessage);
        if ($oItem->hasAttribute($strAttrib) === false) {
            $strError = $this->getFormatedError('HTML Element id = "' . $strID . '" has no attribute  [' . $strAttrib . ']!');
            $this->fail($strMessage ?? $strError);
        }
        return $oItem->getAttribute($strAttrib);
    }
    
    /**
     * Parse a given style attribute into the components it contains.
     * @param string $strStyle
     */
    protected function parseStyle($strStyle) : array
    {
        $aStyle = array();
        $aStyles = explode(';', trim($strStyle));
        foreach ($aStyles as $strStyleDef) {
            $aStyleDef = explode(':', trim($strStyleDef));
            if (count($aStyleDef) == 2) {
                $strName = trim($aStyleDef[0]);
                $strValue = str_replace(';', '', $aStyleDef[1]);
                $aStyle[$strName] = trim($strValue);
            }
        }
        return $aStyle;
    }
    
    protected function getFormatedError(string $strError) : string
    {
        // replace the opening quotation marks with control codes (text color: red) ...
        $strError = str_replace('“', self::AEC_RED, $strError);
        // ... and the closing quotation marks with control codes (text color: standard)
        $strError = str_replace('”', self::AEC_DEFAULT, $strError);
        return $strError;    
    }
    
    /**
     * Get formated list of detailed XML error.
     * this method only works, if <i><b>libxml_use_internal_errors(true);</b></i> is called
     * before parsing the xml and/or validating the xml against XSD file.
     * @return string
     */
    protected function getFormatedXMLError() : string
    {
        $strErrorMsg = '';
        $errors = libxml_get_errors();
        $aLevel = [LIBXML_ERR_WARNING => 'Warning ', LIBXML_ERR_ERROR => 'Error ', LIBXML_ERR_FATAL => 'Fatal Error '];
        $strErrorMsg = 'Error loading HTML:';
        foreach ($errors as $error) {
            $strErrorMsg .= PHP_EOL . $aLevel[$error->level] . $error->code;
            $strErrorMsg .= ' (Line ' . $error->line . ', Col ' . $error->column . ') ';
            $strErrorMsg .= PHP_EOL . trim($error->message);
        }
        return $strErrorMsg;
    }
}