<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use SKien\Formgenerator\FormFlags;
use SKien\Formgenerator\FormLine;
use SKien\Formgenerator\FormStatic;
use SKien\Test\HtmlTestCase;

/**
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormStaticTest extends HtmlTestCase
{
    use FormgeneratorHelper;

    public function test__construct() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oStatic = new FormStatic('just a test static');
        $oFL->add($oStatic);
        $strHTML = $oStatic->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'just a test static'));
    }

    public function test_width() : void
    {
        $oFG = $this->createFG(false);
        $oFG->setColWidth([30, 70]);
        $oFL = $oFG->add(new FormLine('testline'));
        $oStatic = new FormStatic('just a test static');
        $oFL->add($oStatic);
        $strHTML = $oStatic->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'width: 70%'));
    }

    public function test_bold() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oStatic = new FormStatic('just a test static', FormFlags::BOLD);
        $oFL->add($oStatic);
        $strHTML = $oStatic->getHTML();
        $this->assertHtmlTagAttribContains($strHTML, 'div', 'style', 'font-weight: bold');
    }

    public function test_labelFor() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oStatic = new FormStatic('just a test static');
        $oStatic->setLabelFor('test');
        $oFL->add($oStatic);
        $strHTML = $oStatic->getHTML();
        $this->assertHtmlTagAttribContains($strHTML, 'label', 'for', 'test');
    }

    /**
     * @dataProvider provideAlignFlag
     */
    public function test_Align($strStyle, $wFlags) : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oStatic = new FormStatic('just a test static', $wFlags);
        $oFL->add($oStatic);
        $strHTML = $oStatic->getHTML();
        $this->assertNotFalse(strpos($strHTML, $strStyle));
    }

    public function test_Class1() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oStatic = new FormStatic('just a test static');
        $oStatic->setClass('test');
        $oFL->add($oStatic);
        $strHTML = $oStatic->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'class="test"'));
    }

    public function test_Class2() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oStatic = new FormStatic('just a test static', FormFlags::ERROR);
        $oStatic->setClass('test');
        $oFL->add($oStatic);
        $strHTML = $oStatic->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'class="test error"'));
    }

    /**
     * @dataProvider provideClassFlag
     */
    public function test_StdClass($strClass, $wFlags) : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oStatic = new FormStatic('just a test static', $wFlags);
        $oFL->add($oStatic);
        $strHTML = $oStatic->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'class="' . $strClass . '"'));
    }

    public function provideClassFlag()
    {
        return [
            "error" => [
                'error',
                FormFlags::ERROR
            ],
            "hint" => [
                'hint',
                FormFlags::HINT
            ],
            "forminfo" => [
                'forminfo',
                FormFlags::INFO
            ]
        ];
    }
}

