<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Formgenerator\FormLine;
use SKien\Formgenerator\FormTime;
use SKien\Config\JSONConfig;
use SKien\Formgenerator\FormFlags;

/**
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormTimeTest extends TestCase
{
    use FormgeneratorHelper;

    public function test_ValueSet1() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormTime('timeAvailableFrom1'));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'value="20:23"'));
        $this->assertNotFalse(strpos($strHTML, 'data-validation="time'));
    }
    
    public function test_ValueSet2() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormTime('timeAvailableFrom2'));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'value="20:23"'));
        $this->assertNotFalse(strpos($strHTML, 'data-validation="time'));
    }
    
    public function test_ValueSet3() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormTime('timeAvailableFrom3'));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'value="20:23"'));
        $this->assertNotFalse(strpos($strHTML, 'data-validation="time'));
    }
    
    public function test_ValueSetSpecial() : void
    {
        $oFG = $this->createFG(false);
        $oFG->setConfig(new JSONConfig(__DIR__ . DIRECTORY_SEPARATOR . 'testdata/FormGeneratorSpecial.json'));
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormTime('timeAvailableFrom3'));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'value="20#23"'));
        $this->assertNotFalse(strpos($strHTML, 'data-validation="time'));
    }
    
    public function test_ValueNotSet() : void
    {
        $oFG = $this->createFG(true);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormTime('timeNotSet'));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'value=""'));
        $this->assertNotFalse(strpos($strHTML, 'data-validation="time'));
    }
    
    public function test_ValueNoZero() : void
    {
        $oFG = $this->createFG(true);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormTime('timeNotSet', FormFlags::NO_ZERO));
        $strHTML = $oEdit->getHTML();
        $this->assertFalse(strpos($strHTML, 'value='));
        $this->assertNotFalse(strpos($strHTML, 'data-validation="time'));
    }
    
    public function test_Picker() : void
    {
        $oFG = $this->createFG(false);
        $oFG->setConfig(new JSONConfig(__DIR__ . DIRECTORY_SEPARATOR . 'testdata/FormGeneratorSpecial.json'));
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormTime('timeAvailableFrom3', FormFlags::ADD_TIME_PICKER));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'data-picker="time:HH#MM'));
    }
}
