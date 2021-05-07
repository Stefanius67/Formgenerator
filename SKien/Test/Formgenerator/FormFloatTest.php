<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Config\JSONConfig;
use SKien\Formgenerator\FormFloat;
use SKien\Formgenerator\FormFlags;

/**
 * @author Stefanius <s.kientzler@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormFloatTest extends TestCase
{
    use FormgeneratorHelper;

    public function test_ValueSet() : void
    {
        $oFG = $this->createFG(true);
        $oEdit = $oFG->add(new FormFloat('fltDue', 10, 1));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'value="1,904.0"'));
        $this->assertNotFalse(strpos($strHTML, 'data-validation="float'));
    }
    
    public function test_ValueSetDE() : void
    {
        $oFG = $this->createFG(false);
        $oEdit = $oFG->add(new FormFloat('fltDue', 10, 1));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'value="1.904,0"'));
        $this->assertNotFalse(strpos($strHTML, 'data-validation="float'));
    }
    
    public function test_ValueSetSpecial() : void
    {
        $oFG = $this->createFG(false);
        $oFG->setConfig(new JSONConfig(__DIR__ . DIRECTORY_SEPARATOR . 'testdata/FormGeneratorSpecial.json'));
        $oEdit = $oFG->add(new FormFloat('fltDue', 10, 1));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'value="1.904,0"'));
        $this->assertNotFalse(strpos($strHTML, 'data-validation="float'));
    }
    
    public function test_ValueNotSet() : void
    {
        $oFG = $this->createFG(true);
        $oEdit = $oFG->add(new FormFloat('fltNotSet', 10, 1));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'value="0.0"'));
        $this->assertNotFalse(strpos($strHTML, 'data-validation="float'));
    }
    
    public function test_ValueNoZero() : void
    {
        $oFG = $this->createFG(true);
        $oEdit = $oFG->add(new FormFloat('fltNotSet', 10, 1, FormFlags::NO_ZERO));
        $strHTML = $oEdit->getHTML();
        $this->assertFalse(strpos($strHTML, 'value='));
        $this->assertNotFalse(strpos($strHTML, 'data-validation="float'));
    }
}
