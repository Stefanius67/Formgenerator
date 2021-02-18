<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Formgenerator\FormLine;
use SKien\Formgenerator\FormDate;
use SKien\Config\JSONConfig;
use SKien\Formgenerator\FormFlags;

/**
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormDateTest extends TestCase
{
    use FormgeneratorHelper;

    public function test_ValueSet1() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormDate('dateDoB1'));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'value="23.07.1974"'));
        $this->assertNotFalse(strpos($strHTML, 'data-validation="date'));
    }
    
    public function test_ValueSet2() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormDate('dateDoB2'));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'value="23.07.1974"'));
        $this->assertNotFalse(strpos($strHTML, 'data-validation="date'));
    }
    
    public function test_ValueSet3() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormDate('dateDoB3'));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'value="23.07.1974"'));
        $this->assertNotFalse(strpos($strHTML, 'data-validation="date'));
    }
    
    public function test_ValueNotSet() : void
    {
        $oFG = $this->createFG(true);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormDate('dateNotSet'));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'value=""'));
        $this->assertNotFalse(strpos($strHTML, 'data-validation="date'));
    }
    
    public function test_ValueNoZero() : void
    {
        $oFG = $this->createFG(true);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormDate('dateNotSet', FormFlags::NO_ZERO));
        $strHTML = $oEdit->getHTML();
        $this->assertFalse(strpos($strHTML, 'value='));
        $this->assertNotFalse(strpos($strHTML, 'data-validation="date'));
    }
    
    public function test_HTML5_Type() : void
    {
        $oFG = $this->createFG(true);
        $oFG->setConfig(new JSONConfig(__DIR__ . DIRECTORY_SEPARATOR . 'testdata/FormGeneratorSpecial.json'));
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormDate('dateDoB1'));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'value="1974-07-23"'));
        $this->assertFalse(strpos($strHTML, 'data-validation="date'));
    }
    
    public function test_Picker() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormDate('dateDoB2', FormFlags::ADD_DATE_PICKER));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'data-picker="date'));
    }
}
