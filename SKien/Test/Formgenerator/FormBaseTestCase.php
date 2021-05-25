<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

/**
 * Base Testcase with methods to assert that
 * - given text is valid HTML5 document
 * - given text is valid HTML5 block
 * - given text is plain text (doesn't contain any HTML tags)
 *
 * Part of the code from https://gist.github.com/whymarrh/5864443
 *
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormBaseTestCase extends TestCase
{
    /**
     * Assert that the given string is a valid HTML5 document.
     * Validator.nu at <https://about.validator.nu/#tos>.
     * @param string $strHTML The HTML to validate
     */
    public function assertValidHtml(string $strHTML) : void
    {
        // cURL
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://html5.validator.nu/',
            CURLOPT_HTTPHEADER     => ['User-Agent: cURL'],
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_POST           => TRUE,
            CURLOPT_POSTFIELDS     => [
                'out'     => 'xml',
                'content' => $strHTML,
            ],
        ]);
        $strResponse = curl_exec($curl);
        if (!$strResponse) {
            $this->markTestIncomplete('Issues checking HTML validity.');
        }
        curl_close($curl);

        // replace the code markup inside the mixed content with placeholders before XML parsing
        // (simpleXML doesn't support mixed content...)
        $strResponse = str_replace('<code xmlns="http://www.w3.org/1999/xhtml">', '[yellow]', $strResponse);
        $strResponse = str_replace('</code>', '[end]', $strResponse);

        // Fail if errors
        $xml = new SimpleXMLElement($strResponse);
        $nonDocumentErrors = $xml->{'non-document-error'};
        $errors = $xml->error;
        if (count($nonDocumentErrors) > 0) {
            // Indeterminate
            $this->markTestIncomplete();
        } elseif (count($errors) > 0) {
            // Invalid
            $message = "HTML output did not validate:";
            foreach ($errors as $error) {
                $message .= PHP_EOL . '-> ' . $error->message;
            }
            // for terminal output just replace the placeholders with ANSI escape codes
            $message = str_replace('[yellow]', "\e[1;35m", $message);
            $message = str_replace('[end]', "\e[0m", $message);
            $this->fail($message);
        }

        // Valid
        $this->assertTrue(TRUE);
    }

    /**
     * Assert that the given string is a valid HTML5 block.
     * The HTML block is enclsed in a valid HTML5 Document definition before
     * validation.
     * @param string $strHTML The HTML to validate
     */
    public function assertValidHtmlBlock(string $strHTML) : void
    {
        $this->assertValidHtml('<!DOCTYPE html><html><head><title>Test</title></head><body>' . $strHTML . '</body>');
    }

    /**
     * Assert that the given text don't contain any HTML tags (-> contains plain text).
     * @param string $text
     */
    public function assertContainsNoHtmlTag(string $text) : void
    {
        if($text != strip_tags($text)) {
            $this->fail('Text contains HTML tags.');
        }
        $this->assertTrue(TRUE);
    }
}