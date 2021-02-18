<?php
declare(strict_types=1);

namespace SKien\Test\Formgenerator;

use PHPUnit\Framework\TestCase;
use SKien\Formgenerator\FormLine;
use SKien\Formgenerator\FormFlags;
use SKien\Formgenerator\FormCur;

/**
 * @author Stefanius <s.kien@online.de>
 * @copyright MIT License - see the LICENSE file for details
 */
class FormCurTest extends TestCase
{
    use FormgeneratorHelper;

    public function test_ValueSet1() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormCur('fltDue', 10));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'value="1.904,00"'));
        $this->assertNotFalse(strpos($strHTML, 'data-validation="float'));
    }
    
    public function test_ValueNotSet() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormCur('fltNotSet', 10));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, 'value="0,00"'));
        $this->assertNotFalse(strpos($strHTML, 'data-validation="float'));
    }
    
    public function test_ValueNoZero() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormCur('fltNotSet', 10, FormFlags::NO_ZERO));
        $strHTML = $oEdit->getHTML();
        $this->assertFalse(strpos($strHTML, 'value=""'));
        $this->assertNotFalse(strpos($strHTML, 'data-validation="float'));
    }
    
    public function test_ValueCurSymbol() : void
    {
        $oFG = $this->createFG(false);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormCur('fltDue', 10, FormFlags::ADD_CUR));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, '&nbsp;EUR'));
    }
    
    public function test_ValueCurSymbolDefault() : void
    {
        $oFG = $this->createFG(true);
        $oFL = $oFG->add(new FormLine('testline'));
        $oEdit = $oFL->add(new FormCur('fltDue', 10, FormFlags::ADD_CUR));
        $strHTML = $oEdit->getHTML();
        $this->assertNotFalse(strpos($strHTML, '&nbsp;USD'));
    }
}
