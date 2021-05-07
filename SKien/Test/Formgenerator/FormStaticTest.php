<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Formgenerator\FormLine;
use SKien\Formgenerator\FormStatic;
use SKien\Formgenerator\FormFlags;

/**
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormStaticTest extends TestCase
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
        $this->assertNotFalse(strpos($strHTML, 'font-weight: bold'));
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

