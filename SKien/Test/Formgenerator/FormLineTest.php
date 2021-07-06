<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use SKien\Formgenerator\FormInput;
use SKien\Formgenerator\FormLine;
use SKien\Test\HtmlTestCase;

/**
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormLineTest extends HtmlTestCase
{
    use FormgeneratorHelper;

    public function test__construct() : void
    {
        $oFG = $this->createFG(true);
        $oFL = $oFG->add(new FormLine('testline'));
        $this->assertIsObject($oFL);
        $this->assertTrue($oFL instanceof FormLine);
    }

    public function test_setLabelFor()
    {
        $oFG = $this->createFG(true);
        $oFG->setColWidth([20, 80], '%');
        $oFL = $oFG->add(new FormLine('testline'));
        $oFL->setLabelFor('strInput');
        $oFL->add(new FormInput('strInput', 10));
        $strHTML = $oFG->getHTML();
        $this->assertHtmlTagEquals($strHTML, 'label', 'testline');
        $this->assertHtmlTagAttribEquals($strHTML, 'label', 'for', 'strInput');
    }

    public function test_getHTML() : void
    {
        $oFG = $this->createFG(true);
        $oFG->setColWidth([20, 23, 25, 32], '%');
        $oFL = $oFG->add(new FormLine('testline'));
        $oFL->add(new FormInput('strInput1', 10));
        $oFL->add(new FormInput('strInput2', 10));
        $oFL->add(new FormInput('strInput3', 10));
        $strHTML = $oFG->getHTML();
        $this->assertHtmlTagEquals($strHTML, 'label', 'testline');
        $this->assertHtmlHasElement($strHTML, 'strInput1');
        $this->assertHtmlElementAttribEquals($strHTML, 'strInput1', 'tabindex', '1');
        $this->assertHtmlHasElement($strHTML, 'strInput2');
        $this->assertHtmlElementAttribEquals($strHTML, 'strInput2', 'tabindex', '2');
        $this->assertHtmlHasElement($strHTML, 'strInput3');
        $this->assertHtmlElementAttribEquals($strHTML, 'strInput3', 'tabindex', '3');
        $this->assertNotFalse(strpos($strHTML, 'width: 20%'));
        $this->assertNotFalse(strpos($strHTML, 'width: 23%'));
        $this->assertNotFalse(strpos($strHTML, 'width: 25%'));
        $this->assertNotFalse(strpos($strHTML, 'width: 32%'));
        $this->assertValidHtmlBlock($strHTML);
    }

    public function test_HR() : void
    {
        $oFG = $this->createFG(true);
        $oFG->add(new FormLine(FormLine::HR));
        $strHTML = $oFG->getHTML();
        $this->assertNotFalse(strpos($strHTML, '<hr>'));
    }
}

