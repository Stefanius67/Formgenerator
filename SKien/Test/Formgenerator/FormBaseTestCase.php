<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

/**
 * assertValidHtml from https://gist.github.com/whymarrh/5864443
 *
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormBaseTestCase extends TestCase
{
    /**
     * Assert that the given HTML validates
     *
     * Validator.nu at <http://about.validator.nu/#tos>.
     *
     * @param string $html The HTML to validate
     */
    public function assertValidHtml($html)
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
                'content' => $html,
            ],
        ]);
        $response = curl_exec($curl);
        if (!$response) {
            $this->markTestIncomplete('Issues checking HTML validity.');
        }
        curl_close($curl);

        $response = str_replace('<code xmlns="http://www.w3.org/1999/xhtml">', '[yellow]', $response);
        $response = str_replace('</code>', '[end]', $response);

        // Fail if errors
        $xml = new SimpleXMLElement($response);
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
                /*
                $codes = $error->message->code;
                foreach ($codes as $code) {
                    $message .= ' (<' . $code . '>)';
                }
                */
            }
            $message = str_replace('[yellow]', "\e[1;35m", $message);
            $message = str_replace('[end]', "\e[0m", $message);
            $this->fail($message);
        }

        // Valid
        $this->assertTrue(TRUE);
    }

    public function assertValidHtmlBlock($html)
    {
        $this->assertValidHtml('<!DOCTYPE html><html><head><title>Test</title></head><body>' . $html . '</body>');
    }

    public function assertContainsNoHtmlTag($text)
    {
        if($text != strip_tags($text)) {
            $this->fail('Text contains HTML tags.');
        }
        $this->assertTrue(TRUE);
    }
}